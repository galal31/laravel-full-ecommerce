<?php

namespace App\Services\website;

use App\Models\Cart;
use App\Models\CartItem;
use App\Reposetories\website\CheckoutRepository;
use Illuminate\Support\Collection;

class CheckoutService
{
    public function __construct(private CheckoutRepository $checkoutRepository) {}

    public function getCartItems(int $userId): Collection
    {
        return $this->checkoutRepository->getCartItems($userId);
    }

    public function getCartForOrder(int $userId): ?Cart
    {
        return $this->checkoutRepository->getCartForOrder($userId);
    }

    public function getShippingPrice(int $governorateId): float
    {
        return $this->checkoutRepository->getShippingPrice($governorateId);
    }

    public function calculateItemTotal(CartItem $cartItem): float
    {
        $unitPrice = $cartItem->productVariant
            ? (float) $cartItem->productVariant->price
            : $cartItem->product->getPriceAfterDiscount();

        return round($unitPrice * $cartItem->quantity, 2);
    }

    public function calculateSubtotal(Collection $cartItems): float
    {
        return round((float) $cartItems->sum(fn (CartItem $cartItem) => $this->calculateItemTotal($cartItem)), 2);
    }

    public function calculateTotal(float $subtotal, float $shippingPrice, float $couponDiscount): float
    {
        return round(max(0, $subtotal - $couponDiscount) + $shippingPrice, 2);
    }
}
