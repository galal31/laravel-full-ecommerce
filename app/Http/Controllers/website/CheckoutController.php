<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Http\Requests\website\PlaceOrderRequest;
use App\Models\Dashboard\Order;
use App\Services\website\CheckoutService;
use App\Services\website\MyFatoorahService;
use App\Services\website\OrderService;
use Illuminate\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private OrderService $orderService,
        private MyFatoorahService $myFatoorahService
    ) {}

    public function index(): View
    {
        $userId = (int) auth()->id();
        $cartItems = $this->checkoutService->getCartItems($userId);

        return view('website.checkout.index', compact('cartItems'));
    }

    public function callback()
    {
        $paymentId = request('paymentId');
        try {
            if (! is_string($paymentId) || ! ctype_digit($paymentId)) {
                throw new RuntimeException('Invalid payment ID.');
            }
            $paymentStatus = $this->myFatoorahService->getPaymentStatus($paymentId);

            $isPaymentSuccess = $this->myFatoorahService->isPaymentSuccessful($paymentStatus);
            
            if (! $isPaymentSuccess) {
                $failedPayment = $this->myFatoorahService->getFailedPayment($paymentStatus);
                if ($failedPayment) {
                    $order = $this->orderService->getOrderForFailedPayment($failedPayment);
                    if (in_array($order->status, ['completed', 'delivered'], true) || $order->payment_review_reason) {
                        return redirect()->route('website.checkout.success', ['order' => $order->id]);
                    }
                }
                throw new RuntimeException('Payment was not successful.');
            }
            $order = $this->orderService->completePayment($isPaymentSuccess);
            return redirect()->route('website.checkout.success', ['order' => $order->id]);

        } catch (ConnectionException|RuntimeException|ModelNotFoundException $e) {
            // لا نرجع للـ Referer لأنه قد يكون موقع بوابة الدفع.
            return response()->view('website.checkout.payment-failed', [], 422);
        }
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());
        try {
            $invoice = $this->myFatoorahService->createInvoice($order);
        } catch (ConnectionException|RuntimeException $e) {
            throw ValidationException::withMessages([
                'payment' => app()->getLocale() === 'ar'
                    ? "تم حفظ الطلب رقم {$order->id}، لكن تعذر تجهيز رابط الدفع. تواصل مع الدعم بهذا الرقم."
                    : "Order {$order->id} was saved, but the payment link could not be prepared. Contact support with this number.",
            ]);
        }

        return redirect()->away($invoice['payment_url']);
    }

    public function success(Order $order): View
    {
        // لا نعرض بيانات الطلب إلا لصاحبه.
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);
        $order->load('items');

        return view('website.checkout.success', compact('order'));
    }
}
