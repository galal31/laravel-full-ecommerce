<?php

namespace App\Reposetories\website;

use App\Models\Cart;
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
}
