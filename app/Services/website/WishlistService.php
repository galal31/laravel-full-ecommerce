<?php

namespace App\Services\website;

use App\Models\Dashboard\Product;
use App\Models\Dashboard\Wishlist;

class WishlistService
{
    public function toggle(int $userId, int $productId): bool
    {
        Product::query()
            ->active()
            ->findOrFail($productId);

        $wishlistItem = Wishlist::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();

            return false;
        }

        Wishlist::query()->create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return true;
    }
}
