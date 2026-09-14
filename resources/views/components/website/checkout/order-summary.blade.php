<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public $cartItems;
    public float $shippingPrice = 0;

    public function mount($cartItems)
    {
        $this->cartItems = $cartItems;
    }

    #[On('shipping_price_updated')]
    public function updateShippingPrice($shippingPrice): void
    {
        $this->shippingPrice = max(0, (float) $shippingPrice);
    }

    public function subtotal(): float
    {
        return $this->cartItems->sum(function ($cartItem) {
            $unitPrice = $cartItem->productVariant
                ? (float) $cartItem->productVariant->price
                : $cartItem->product->getPriceAfterDiscount();

            return $unitPrice * $cartItem->quantity;
        });
    }

    public function total(): float
    {
        return $this->subtotal() + $this->shippingPrice;
    }
};
?>

<aside class="checkout-card checkout-order-summary-card">
    <div class="checkout-card-heading">
        <h2>{{ __('website.checkout_order_summary') }}</h2>
        <p>{{ __('website.checkout_order_summary_hint') }}</p>
    </div>

    @if ($cartItems->isNotEmpty())
        <div class="checkout-summary-list">
            @foreach ($cartItems as $cartItem)
                @php
                    $product = $cartItem->product;
                    $variant = $cartItem->productVariant;
                    $hasDiscount = ! $variant && $product->hasActiveDiscount();
                    $unitPrice = $variant
                        ? (float) $variant->price
                        : $product->getPriceAfterDiscount();
                    $lineTotal = $unitPrice * $cartItem->quantity;
                    $variantName = $variant?->attributeValues
                        ->pluck('value')
                        ->filter()
                        ->join(' | ');
                    $image = $variant?->images->first()
                        ?? $product->images->first();
                @endphp

                <article class="checkout-summary-item" wire:key="checkout-item-{{ $cartItem->id }}">
                    <a
                        href="{{ route('website.products.show', $product->slug) }}"
                        class="checkout-summary-image"
                    >
                        <img
                            src="{{ $image?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                            alt="{{ $product->name }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </a>

                    <div class="checkout-summary-info">
                        <a
                            href="{{ route('website.products.show', $product->slug) }}"
                            class="checkout-summary-name"
                        >
                            {{ $product->name }}
                        </a>

                        <p class="checkout-summary-meta">
                            {{ $variantName ?: ($product->brand?->name ?? __('website.no_brand')) }}
                        </p>

                        <div class="checkout-summary-price-row">
                            <span class="checkout-summary-quantity">
                                {{ __('website.quantity') }}: {{ $cartItem->quantity }}
                            </span>

                            <span class="checkout-summary-price">
                                @if ($hasDiscount)
                                    <span class="checkout-summary-old-price">
                                        {{ number_format((float) $product->price * $cartItem->quantity, 2) }}
                                    </span>
                                @endif

                                {{ number_format($lineTotal, 2) }}
                                {{ __('website.currency') }}
                            </span>
                        </div>

                        @if ($hasDiscount)
                            <span class="checkout-summary-discount">
                                {{ __('website.discount_badge', ['percentage' => (float) $product->discount]) }}
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="checkout-summary-totals">
            <div class="checkout-summary-total-row">
                <span>{{ __('website.cart_subtotal') }}</span>
                <strong>
                    {{ number_format($this->subtotal(), 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>

            <div class="checkout-summary-total-row">
                <span>{{ __('governorates.shipping_price') }}</span>
                <strong>
                    {{ number_format($shippingPrice, 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>

            <div class="checkout-summary-total-row is-total">
                <span>{{ __('website.cart_total') }}</span>
                <strong>
                    {{ number_format($this->total(), 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>
        </div>
    @else
        <div class="checkout-summary-placeholder">
            {{ __('website.cart_empty') }}
        </div>
    @endif
</aside>
