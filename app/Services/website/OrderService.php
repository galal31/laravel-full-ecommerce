<?php

namespace App\Services\website;

use App\Models\Dashboard\City;
use App\Models\Dashboard\Order;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\ProductVariant;
use App\Models\Dashboard\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private CheckoutService $checkoutService,
        private CouponService $couponService
    ) {}

    public function createOrder($requestData)
    {
        return $this->storeOrder($requestData);
    }

    public function getShippingDetailes($requestData)
    {
        $city = City::findOrFail($requestData['city_id']);
        $governorate = $city->governorate;
        $country = $governorate->country;
        $shippingDetailes = [
            'country' => $country->name,
            'governorate' => $governorate->name,
            'city' => $city->name,
            'street' => $requestData['street'],
        ];

        return $shippingDetailes;
    }

    public function storeOrder($requestData)
    {

        $shippingDetailes = $this->getShippingDetailes($requestData);
        DB::beginTransaction();
        try {
            // نجلب السلة وعلاقاتها بالأقفال بعد بدء معاملة قاعدة البيانات.
            $cart = $this->checkoutService->getCartForOrder(auth()->id());

            // بداية التحقق من السلة والمنتجات.
            if (! $cart) {
                throw ValidationException::withMessages(['cart' => __('website.cart_empty')]);
            }

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => __('website.cart_empty')]);
            }

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                $variant = $cartItem->productVariant;

                // المنتج يجب أن يكون موجودًا ونشطًا ومتاحًا للبيع.
                if (! $product || ! $product->status || ! $product->available_in_stock) {
                    throw ValidationException::withMessages(['cart' => __('website.product_not_available')]);
                }

                // لا نقبل كمية صفر أو كمية سالبة.
                if ($cartItem->quantity < 1) {
                    throw ValidationException::withMessages(['cart' => __('website.invalid_cart_quantity')]);
                }

                // لو السلة تحتوي رقم متغير، يجب أن يكون موجودًا ويتبع نفس المنتج.
                if ($cartItem->product_variant_id !== null
                    && (! $variant || $variant->product_id !== $product->id)) {
                    throw ValidationException::withMessages(['cart' => __('website.selected_variant_not_found')]);
                }

                // المنتج ذو المتغيرات لا يجوز طلبه بدون اختيار متغير.
                if (! $variant && $product->variants()->exists()) {
                    throw ValidationException::withMessages(['cart' => __('website.select_variant_to_add_to_cart')]);
                }

                // المتغير يعتمد على مخزونه، والمنتج العادي على كميته عند تفعيل إدارة المخزون.
                if (($variant && $cartItem->quantity > $variant->stock)
                    || (! $variant && $product->manage_stock && $cartItem->quantity > $product->quantity)) {
                    throw ValidationException::withMessages(['cart' => __('website.not_enough_stock')]);
                }
            }
            // نهاية التحقق من المنتجات: لم نحجز المخزون أو ننقصه هنا.

            // نعيد التحقق لأن الكوبون قد يتغير بعد تطبيقه في السلة.
            $coupon = null;
            if ($cart->coupon_id !== null) {
                try {
                    $coupon = $this->couponService->reserveCouponForOrder($cart->coupon_id);
                } catch (\DomainException $e) {
                    // نعرض سبب الرفض في صفحة الشراء، والتراجع يحافظ على السلة.
                    throw ValidationException::withMessages(['coupon' => $e->getMessage()]);
                }
            }

            // نحسب الأسعار بعد نجاح التحقق وباستخدام المنتجات التي قرأناها الآن.
            $subtotal = $this->checkoutService->calculateSubtotal($cart->items);
            $couponDiscount = $coupon ? $coupon->discount_percentage * $subtotal / 100 : 0;
            $shippingPrice = $this->checkoutService->getShippingPrice($requestData['governorate_id']);
            $total = $this->checkoutService->calculateTotal($subtotal, $shippingPrice, $couponDiscount);

            $order = auth()->user()->orders()->create([
                'coupon_id' => $coupon ? $coupon->id : null,
                'coupon_code' => $coupon ? $coupon->code : null,
                'coupon_discount_percentage' => $coupon ? $coupon->discount_percentage : 0,
                'coupon_discount_amount' => $couponDiscount,
                'coupon_usage_status' => $coupon ? 'reserved' : null,
                // نحفظ موعد انتهاء مهلة الدفع فقط؛ فحص الدفع وتحرير الحجز يأتيان لاحقًا.
                'payment_expires_at' => now()->addMinutes(30),
                'user_name' => $requestData['user_name'],
                'user_phone' => $requestData['user_phone'],
                'user_email' => $requestData['user_email'],
                'price' => $subtotal,
                'shipping_price' => $shippingPrice,
                'total_price' => $total,
                'note' => $requestData['note'] ?? null,
                'status' => 'pending',
                'country' => $shippingDetailes['country'],
                'governorate' => $shippingDetailes['governorate'],
                'city' => $shippingDetailes['city'],
                'street' => $shippingDetailes['street'],
            ]);
            // create order items data
            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->productVariant;

                //  ['Color' => 'Red', 'Size' => 'M']

                $variantData = [];
                if ($variant) {
                    foreach (['ar','en'] as $lang){
                        $variantData[$lang] = $variant->attributeValues->mapWithKeys(function ($attributeValue) use ($lang) {
                            return [$attributeValue->attribute->getTranslation('name', $lang) => $attributeValue->getTranslation('value', $lang)];
                        })->toArray();
                    }
                }

                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_quantity' => $cartItem->quantity,
                    'product_name' => $cartItem->product->getTranslations('name'),
                    'product_price' => $cartItem->productVariant ? $cartItem->productVariant->price : $cartItem->product->getPriceAfterDiscount(),
                    'data' => $cartItem->productVariant ? $variantData : null,
                ]);
            }
            // delete cart items and coupon after order is created
            
            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);
            DB::commit();

            return $order;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function getTransactionOrder($invoiceId)
    {
        $transaction = Transaction::where('invoice_id', $invoiceId)->firstOrFail();
        $order = $transaction->order;

        return $order;
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = auth()->user()->orders()->findOrFail($orderId);
        $order->update(['status' => $status]);
    }

    public function completePayment(array $payment)
    {
        return DB::transaction(function () use ($payment) {
            $transaction = Transaction::where('invoice_id', $payment['invoice_id'])->lockForUpdate()->firstOrFail();
            $order = $transaction->order()->lockForUpdate()->firstOrFail();

            // نطابق مبلغ وعملة الدفع قبل اعتماد الطلب، بدون الاعتماد على جلسة المتصفح.
            if (! is_numeric($payment['amount'])
                || abs((float) $payment['amount'] - (float) $order->total_price) > 0.005
                || $payment['currency'] !== config('services.myfatoorah.currency')
                || ! in_array($order->status, ['pending', 'completed', 'delivered'], true)
                || ($transaction->transaction_id && $transaction->transaction_id !== $payment['transaction_id'])) {
                throw new \RuntimeException('Payment does not match the order.');
            }

            $transaction->update(['transaction_id' => $payment['transaction_id']]);
            if ($order->status === 'pending') {
                try {
                    // معاملة داخلية: لو تعذر خصم أي عنصر، نرجع خصم كل عناصر الطلب.
                    DB::transaction(function () use ($order) {
                        $this->decreaseOrderStock($order);
                        $this->couponService->confirmCouponUsage($order);
                    });
                } catch (\DomainException $e) {
                    $reason = $e->getMessage();
                    if ($reason === 'stock_unavailable') {
                        // الدفع نجح بالفعل؛ الاستخدام يحسب حتى لو احتاج تنفيذ الطلب مراجعة.
                        try {
                            $this->couponService->confirmCouponUsage($order);
                        } catch (\DomainException $couponError) {
                            $reason = $couponError->getMessage();
                        }
                    }
                    $order->update(['payment_review_reason' => $reason]);

                    return $order;
                }

                $order->update(['status' => 'completed', 'payment_review_reason' => null]);
            }

            return $order;
        });
    }

    // نطابق الطلب فقط؛ فشل محاولة واحدة لا ينهي مهلة الدفع ولا يحرر حجز الكوبون.
    public function getOrderForFailedPayment(array $payment): Order
    {
        return DB::transaction(function () use ($payment) {
            $transaction = Transaction::where('invoice_id', $payment['invoice_id'])
                ->lockForUpdate()->firstOrFail();
            $order = $transaction->order()->lockForUpdate()->firstOrFail();

            if (! is_numeric($payment['amount'])
                || abs((float) $payment['amount'] - (float) $order->total_price) > 0.005
                || $payment['currency'] !== config('services.myfatoorah.currency')) {
                throw new \RuntimeException('Payment does not match the order.');
            }

            return $order;
        });
    }

    private function decreaseOrderStock(Order $order): void
    {
        $items = $order->items()->orderBy('product_id')->orderBy('id')->lockForUpdate()->get();
        if ($items->isEmpty()) {
            throw new \DomainException('stock_unavailable');
        }

        // نفس ترتيب أقفال إنشاء الطلب: المنتجات أولًا ثم المتغيرات.
        $products = Product::whereIn('id', $items->pluck('product_id')->all())
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $items->pluck('product_variant_id')->all())
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item->product_id);
            if (! $product || $item->product_quantity < 1) {
                throw new \DomainException('stock_unavailable');
            }

            if ($item->product_variant_id !== null) {
                $variant = $variants->get($item->product_variant_id);
                if (! $variant || $variant->product_id !== $product->id) {
                    throw new \DomainException('stock_unavailable');
                }

                $updated = ProductVariant::whereKey($variant->id)
                    ->where('stock', '>=', $item->product_quantity)
                    ->decrement('stock', $item->product_quantity);
            } else {
                // حذف المتغير يفرغ رقمه، لكنه لا يحول عنصره إلى منتج عادي.
                if ($item->getTranslations('data') !== []) {
                    throw new \DomainException('stock_unavailable');
                }
                if (! $product->manage_stock) {
                    continue;
                }

                $updated = Product::whereKey($product->id)
                    ->where('quantity', '>=', $item->product_quantity)
                    ->decrement('quantity', $item->product_quantity);
            }

            // الشرط داخل تحديث قاعدة البيانات يمنع المخزون السالب حتى مع صفوف متكررة.
            if ($updated !== 1) {
                throw new \DomainException('stock_unavailable');
            }
        }
    }
}
