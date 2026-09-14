<?php

namespace App\Services\website;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Dashboard\Product;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getItems(int $userId): Collection
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->with([
                'items.product.brand',
                'items.product.images',
                'items.productVariant.images',
                'items.productVariant.attributeValues',
            ])
            ->first();

        return $cart?->items ?? collect();
    }

    public function add(
        int $userId,
        int $productId,
        ?int $variantId,
        int $quantity
    ): array {
        if ($quantity < 1) {
            throw new DomainException(__('website.invalid_cart_quantity'));
        }

        return DB::transaction(function () use ($userId, $productId, $variantId, $quantity) {
            $product = Product::query()
                ->active()
                ->lockForUpdate()
                ->find($productId);

            if (! $product || ! $product->available_in_stock) {
                throw new DomainException(__('website.product_not_available'));
            }

            $hasVariants = $product->variants()->exists();
            $variant = null;

            if ($hasVariants) {
                if ($variantId === null) {
                    throw new DomainException(__('website.select_variant_to_add_to_cart'));
                }

                $variant = $product->variants()
                    ->lockForUpdate()
                    ->find($variantId);

                if (! $variant) {
                    throw new DomainException(__('website.selected_variant_not_found'));
                }
            } elseif ($variantId !== null) {
                throw new DomainException(__('website.selected_variant_not_found'));
            }

            Cart::query()->insertOrIgnore([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cart = Cart::query()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $itemQuery = $cart->items()->where('product_id', $product->id);

            $variant
                ? $itemQuery->where('product_variant_id', $variant->id)
                : $itemQuery->whereNull('product_variant_id');

            $item = $itemQuery->first();
            $newQuantity = ($item?->quantity ?? 0) + $quantity;

            $this->ensureStockIsAvailable($product, $variant?->stock, $newQuantity);

            if ($item) {
                $item->update(['quantity' => $newQuantity]);

                return ['item' => $item->fresh(), 'created' => false];
            }

            $item = $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $newQuantity,
            ]);

            return ['item' => $item, 'created' => true];
        });
    }

    public function increase(int $userId, int $cartItemId): CartItem
    {
        return DB::transaction(function () use ($userId, $cartItemId) {
            $item = $this->ownedItem($userId, $cartItemId, true);
            $newQuantity = $item->quantity + 1;

            $this->ensureStockIsAvailable(
                $item->product,
                $item->productVariant?->stock,
                $newQuantity
            );

            $item->update(['quantity' => $newQuantity]);

            return $item->fresh(['product', 'productVariant']);
        });
    }

    public function decrease(int $userId, int $cartItemId): CartItem
    {
        $item = $this->ownedItem($userId, $cartItemId);

        if ($item->quantity <= 1) {
            throw new DomainException(__('website.cart_min_quantity_reached'));
        }

        $item->decrement('quantity');

        return $item->fresh(['product', 'productVariant']);
    }

    public function delete(int $userId, int $cartItemId): string
    {
        $item = $this->ownedItem($userId, $cartItemId);
        $productName = (string) $item->product->name;

        $item->delete();

        return $productName;
    }

    private function ownedItem(int $userId, int $cartItemId, bool $lock = false): CartItem
    {
        $query = CartItem::query()
            ->whereKey($cartItemId)
            ->whereHas('cart', fn ($cartQuery) => $cartQuery->where('user_id', $userId))
            ->with(['product', 'productVariant']);

        if ($lock) {
            $query->lockForUpdate();
        }

        $item = $query->first();

        if (! $item) {
            throw new DomainException(__('website.cart_item_not_found'));
        }

        return $item;
    }

    private function ensureStockIsAvailable(
        Product $product,
        ?int $variantStock,
        int $requestedQuantity
    ): void {
        if (! $product->status || ! $product->available_in_stock) {
            throw new DomainException(__('website.product_not_available'));
        }

        if ($variantStock !== null && $requestedQuantity > $variantStock) {
            throw new DomainException(__('website.not_enough_stock'));
        }

        if ($variantStock === null && $product->manage_stock && $requestedQuantity > $product->quantity) {
            throw new DomainException(__('website.not_enough_stock'));
        }
    }
}
