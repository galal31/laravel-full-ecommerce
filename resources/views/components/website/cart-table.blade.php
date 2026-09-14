<?php

use App\Services\website\CartService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    #[Computed]
    public function cartItems(): Collection
    {
        return app(CartService::class)->getItems((int) auth()->id());
    }

    #[Computed]
    public function subtotal(): float
    {
        return $this->cartItems->sum(function ($cartItem) {
            $unitPrice = $cartItem->productVariant
                ? (float) $cartItem->productVariant->price
                : $cartItem->product->getPriceAfterDiscount();

            return $unitPrice * $cartItem->quantity;
        });
    }

    public function increaseQuantity(int $cartItemId): void
    {
        try {
            $item = app(CartService::class)->increase((int) auth()->id(), $cartItemId);
            $this->forgetTotals();
            $this->showSuccess(__('website.cart_quantity_increased', [
                'product' => $item->product->name,
            ]));
        } catch (\DomainException $exception) {
            $this->showError($exception->getMessage());
        }
    }

    public function decreaseQuantity(int $cartItemId): void
    {
        try {
            $item = app(CartService::class)->decrease((int) auth()->id(), $cartItemId);
            $this->forgetTotals();
            $this->showSuccess(__('website.cart_quantity_decreased', [
                'product' => $item->product->name,
            ]));
        } catch (\DomainException $exception) {
            $this->showError($exception->getMessage());
        }
    }

    public function deleteItem(int $cartItemId): void
    {
        try {
            $productName = app(CartService::class)->delete(
                (int) auth()->id(),
                $cartItemId
            );
            $this->forgetTotals();
            $this->dispatch('cart_item_deleted');
            $this->showSuccess(__('website.cart_item_deleted_successfully', [
                'product' => $productName,
            ]));
        } catch (\DomainException $exception) {
            $this->showError($exception->getMessage());
        }
    }

    private function forgetTotals(): void
    {
        unset($this->cartItems, $this->subtotal);
    }

    private function showSuccess(string $message): void
    {
        $this->successMessage = $message;
        $this->errorMessage = null;
    }

    private function showError(string $message): void
    {
        $this->errorMessage = $message;
        $this->successMessage = null;
    }
};
?>

<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <style>
        .cart-page-heading {
            background: #fffafe;
            border-bottom: 1px solid #f5e7f1;
            padding: 5rem 0;
            text-align: center;
        }

        .cart-page-heading h1 {
            color: #232532;
            font-size: 3.6rem;
            margin: 0 0 1rem;
        }

        .cart-page-heading p {
            color: #797979;
            font-size: 1.5rem;
            margin: 0;
        }

        .cart-page-content {
            padding: 6rem 0;
        }

        .cart-feedback {
            border: 1px solid transparent;
            border-radius: 0.9rem;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 2rem;
            padding: 1.3rem 1.5rem;
        }

        .cart-feedback.is-success {
            background: #eaf8ef;
            border-color: #ccebd7;
            color: #238636;
        }

        .cart-feedback.is-error {
            background: #fff0f0;
            border-color: #f2cccc;
            color: #c62828;
        }

        .cart-layout {
            align-items: start;
            display: grid;
            gap: 2.4rem;
            grid-template-columns: minmax(0, 1fr) 31rem;
        }

        .cart-table-wrapper,
        .cart-summary {
            background: #fff;
            border: 1px solid #eee4ec;
            border-radius: 1.2rem;
            box-shadow: 0 1.2rem 3rem rgba(35, 37, 50, 0.06);
            overflow: hidden;
        }

        .cart-table {
            border-collapse: collapse;
            margin: 0;
            width: 100%;
        }

        .cart-table th {
            background: #fff6fc;
            color: #232532;
            font-size: 1.3rem;
            font-weight: 700;
            padding: 1.8rem 1.5rem;
            text-align: start;
            white-space: nowrap;
        }

        .cart-table td {
            border-top: 1px solid #f1e8ef;
            color: #565664;
            font-size: 1.4rem;
            padding: 1.8rem 1.5rem;
            vertical-align: middle;
        }

        .cart-product {
            align-items: center;
            display: flex;
            gap: 1.4rem;
            min-width: 23rem;
        }

        .cart-product-image {
            align-items: center;
            background: #fffafe;
            border-radius: 0.9rem;
            display: flex;
            flex: 0 0 8rem;
            height: 8rem;
            justify-content: center;
            overflow: hidden;
            width: 8rem;
        }

        .cart-product-image img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .cart-product-name {
            color: #232532;
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.5;
        }

        .cart-product-meta {
            color: #8a8994;
            font-size: 1.2rem;
            margin-top: 0.4rem;
        }

        .cart-price,
        .cart-line-total {
            color: #ae1c9a;
            font-weight: 700;
            white-space: nowrap;
        }

        .cart-quantity-control {
            align-items: center;
            border: 1px solid #e4d8e1;
            border-radius: 0.8rem;
            display: inline-flex;
            overflow: hidden;
        }

        .cart-quantity-button {
            align-items: center;
            background: #fff;
            border: 0;
            color: #ae1c9a;
            display: inline-flex;
            font-size: 1.8rem;
            height: 3.8rem;
            justify-content: center;
            transition: background-color 160ms ease, color 160ms ease;
            width: 3.8rem;
        }

        .cart-quantity-button:hover,
        .cart-quantity-button:focus-visible {
            background: #ae1c9a;
            color: #fff;
            outline: none;
        }

        .cart-quantity-value {
            border-inline: 1px solid #e4d8e1;
            color: #232532;
            font-weight: 700;
            min-width: 4rem;
            padding: 0.8rem;
            text-align: center;
        }

        .cart-delete-button {
            align-items: center;
            background: #fff;
            border: 1px solid #dc3545;
            border-radius: 0.7rem;
            color: #dc3545;
            display: inline-flex;
            height: 3.8rem;
            justify-content: center;
            transition: background-color 160ms ease, color 160ms ease;
            width: 3.8rem;
        }

        .cart-delete-button:hover,
        .cart-delete-button:focus-visible {
            background: #dc3545;
            color: #fff;
            outline: none;
        }

        .cart-summary {
            padding: 2.4rem;
            position: sticky;
            top: 2rem;
        }

        .cart-summary h2 {
            color: #232532;
            font-size: 2.2rem;
            margin: 0 0 2rem;
        }

        .cart-summary-row {
            align-items: center;
            border-bottom: 1px solid #f0e5ed;
            display: flex;
            font-size: 1.5rem;
            justify-content: space-between;
            padding-bottom: 1.6rem;
        }

        .cart-summary-total {
            color: #ae1c9a;
            font-size: 2rem;
            font-weight: 800;
        }

        .cart-checkout-button {
            align-items: center;
            background: #ae1c9a;
            border: 1px solid #ae1c9a;
            border-radius: 0.8rem;
            color: #fff;
            display: flex;
            font-size: 1.4rem;
            font-weight: 700;
            justify-content: center;
            margin-top: 2rem;
            min-height: 4.8rem;
            padding: 1rem 1.5rem;
            transition: background-color 160ms ease, transform 160ms ease;
            width: 100%;
        }

        .cart-checkout-button:hover {
            background: #8f177f;
            color: #fff;
            transform: translateY(-1px);
        }

        .cart-checkout-button:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .cart-empty {
            background: #fff;
            border: 1px dashed #dfcddd;
            border-radius: 1.2rem;
            box-shadow: 0 1.2rem 3rem rgba(35, 37, 50, 0.04);
            padding: 6rem 2rem;
            text-align: center;
        }

        .cart-empty-icon {
            align-items: center;
            background: #fff1fb;
            border-radius: 50%;
            color: #ae1c9a;
            display: inline-flex;
            height: 8rem;
            justify-content: center;
            margin-bottom: 2rem;
            width: 8rem;
        }

        .cart-empty h2 {
            color: #232532;
            font-size: 2.4rem;
            margin-bottom: 1rem;
        }

        .cart-empty p {
            color: #797979;
            font-size: 1.4rem;
            margin-bottom: 2rem;
        }

        .cart-continue-button {
            background: #ae1c9a;
            border-radius: 0.8rem;
            color: #fff;
            display: inline-flex;
            font-size: 1.4rem;
            font-weight: 700;
            padding: 1.1rem 1.8rem;
        }

        .cart-continue-button:hover {
            color: #fff;
        }

        @media (max-width: 991px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .cart-page-heading {
                padding: 3rem 0;
            }

            .cart-page-heading h1 {
                font-size: 2.8rem;
            }

            .cart-page-content {
                padding: 3.5rem 0;
            }

            .cart-table-wrapper {
                background: transparent;
                border: 0;
                box-shadow: none;
                overflow: visible;
            }

            .cart-table,
            .cart-table tbody,
            .cart-table tr,
            .cart-table td {
                display: block;
                width: 100%;
            }

            .cart-table thead {
                display: none;
            }

            .cart-table tr {
                background: #fff;
                border: 1px solid #eee4ec;
                border-radius: 1.2rem;
                box-shadow: 0 1rem 2.5rem rgba(35, 37, 50, 0.05);
                margin-bottom: 1.5rem;
                overflow: hidden;
            }

            .cart-table td {
                align-items: center;
                border-top: 1px solid #f1e8ef;
                display: flex;
                justify-content: space-between;
                padding: 1.3rem 1.5rem;
                text-align: end;
            }

            .cart-table td:first-child {
                border-top: 0;
                display: block;
                text-align: start;
            }

            .cart-table td:not(:first-child)::before {
                color: #232532;
                content: attr(data-label);
                font-weight: 700;
                margin-inline-end: 1rem;
                text-align: start;
            }

            .cart-product {
                min-width: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .cart-quantity-button,
            .cart-delete-button,
            .cart-checkout-button {
                transition: none;
            }
        }
    </style>

    <section class="cart-page-heading">
        <div class="container">
            <h1>{{ __('website.cart_title') }}</h1>
            <p>{{ __('website.cart_subtitle') }}</p>
        </div>
    </section>

    <section class="cart-page-content">
        <div class="container">
            @if ($successMessage)
                <div class="cart-feedback is-success" role="status" aria-live="polite">
                    {{ $successMessage }}
                </div>
            @elseif ($errorMessage)
                <div class="cart-feedback is-error" role="alert" aria-live="assertive">
                    {{ $errorMessage }}
                </div>
            @endif

            @if ($this->cartItems->isNotEmpty())
                <div class="cart-layout">
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>{{ __('website.cart_product') }}</th>
                                    <th>{{ __('website.cart_unit_price') }}</th>
                                    <th>{{ __('website.quantity') }}</th>
                                    <th>{{ __('website.cart_total') }}</th>
                                    <th>{{ __('website.cart_actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($this->cartItems as $cartItem)
                                    @php
                                        $product = $cartItem->product;
                                        $variant = $cartItem->productVariant;
                                        $unitPrice = $variant
                                            ? (float) $variant->price
                                            : $product->getPriceAfterDiscount();
                                        $variantName = $variant?->attributeValues
                                            ->pluck('value')
                                            ->filter()
                                            ->join(' | ');
                                        $mainImage = $variant?->images->first()
                                            ?? $product->images->first();
                                    @endphp

                                    <tr wire:key="cart-row-{{ $cartItem->id }}">
                                        <td>
                                            <div class="cart-product">
                                                <a
                                                    href="{{ route('website.products.show', $product->slug) }}"
                                                    class="cart-product-image"
                                                >
                                                    <img
                                                        src="{{ $mainImage?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                                        alt="{{ $product->name }}"
                                                        loading="lazy"
                                                        decoding="async"
                                                    >
                                                </a>

                                                <div>
                                                    <a
                                                        href="{{ route('website.products.show', $product->slug) }}"
                                                        class="cart-product-name"
                                                    >
                                                        {{ $product->name }}
                                                    </a>

                                                    <p class="cart-product-meta">
                                                        {{ $variantName ?: ($product->brand?->name ?? __('website.no_brand')) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td data-label="{{ __('website.cart_unit_price') }}">
                                            <span class="cart-price">
                                                {{ number_format($unitPrice, 2) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        </td>

                                        <td data-label="{{ __('website.quantity') }}">
                                            <div class="cart-quantity-control">
                                                <button
                                                    type="button"
                                                    class="cart-quantity-button"
                                                    aria-label="{{ __('website.cart_decrease_quantity', ['product' => $product->name]) }}"
                                                    wire:click="decreaseQuantity({{ $cartItem->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="decreaseQuantity({{ $cartItem->id }})"
                                                >−</button>

                                                <span class="cart-quantity-value">{{ $cartItem->quantity }}</span>

                                                <button
                                                    type="button"
                                                    class="cart-quantity-button"
                                                    aria-label="{{ __('website.cart_increase_quantity', ['product' => $product->name]) }}"
                                                    wire:click="increaseQuantity({{ $cartItem->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="increaseQuantity({{ $cartItem->id }})"
                                                >+</button>
                                            </div>
                                        </td>

                                        <td data-label="{{ __('website.cart_total') }}">
                                            <span class="cart-line-total">
                                                {{ number_format($unitPrice * $cartItem->quantity, 2) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        </td>

                                        <td data-label="{{ __('website.cart_actions') }}">
                                            <button
                                                type="button"
                                                class="cart-delete-button"
                                                aria-label="{{ __('website.cart_delete_product', ['product' => $product->name]) }}"
                                                wire:click="deleteItem({{ $cartItem->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteItem({{ $cartItem->id }})"
                                            >
                                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <aside class="cart-summary">
                        <h2>{{ __('website.cart_summary') }}</h2>

                        <div class="cart-summary-row">
                            <span>{{ __('website.cart_subtotal') }}</span>
                            <span class="cart-summary-total">
                                {{ number_format($this->subtotal, 2) }}
                                {{ __('website.currency') }}
                            </span>
                        </div>

                        @if (Route::has('website.checkout.index'))
                            <a href="{{ route('website.checkout.index') }}" class="cart-checkout-button">
                                {{ __('website.cart_checkout') }}
                            </a>
                        @else
                            <button type="button" class="cart-checkout-button" disabled>
                                {{ __('website.cart_checkout') }}
                            </button>
                        @endif
                    </aside>
                </div>
            @else
                <div class="cart-empty">
                    <span class="cart-empty-icon" aria-hidden="true">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                            <path d="M2.25 2.25h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h2>{{ __('website.cart_empty') }}</h2>
                    <p>{{ __('website.cart_empty_hint') }}</p>
                    <a href="{{ route('website.home') }}" class="cart-continue-button">
                        {{ __('website.cart_continue_shopping') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
