<?php

namespace App\Reposetories\website;

use App\Models\Cart;
use App\Models\Dashboard\ShippingGovernorate;
use Illuminate\Support\Collection;

class CheckoutRepository
{
    public function getCartItems(int $userId): Collection
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->with([
                'items.product.images',
                'items.product.brand',
                'items.productVariant.attributeValues',
                'items.productVariant.images',
            ])
            ->first();

        return $cart ? $cart->items : collect();
    }

    // تُستدعى داخل معاملة قاعدة البيانات؛ الأقفال تستمر حتى اعتماد الحفظ أو التراجع عنه.
    public function getCartForOrder(int $userId): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->with([
                'items' => fn ($query) => $query->orderBy('product_id')->orderBy('id')->lockForUpdate(),
                'items.product' => fn ($query) => $query->orderBy('id')->lockForUpdate(),
                'items.productVariant' => fn ($query) => $query->orderBy('id')->lockForUpdate(),
                'items.productVariant.attributeValues.attribute',
            ])
            ->first();
    }

    public function getShippingPrice(int $governorateId): float
    {
        $price = ShippingGovernorate::where('governorate_id', $governorateId)->value('price');
        return $price !== null ? (float) $price : 0.00;
    }
}
