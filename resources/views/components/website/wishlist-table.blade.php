<?php

use App\Models\Dashboard\Wishlist;
use Livewire\Component;

new class extends Component
{
    public $wishlistItems;

    public function mount(): void
    {
        $this->wishlistItems = Wishlist::query()
            ->select(['id', 'user_id', 'product_id', 'created_at'])
            ->where('user_id', auth()->id())
            ->whereHas('product', fn ($productQuery) => $productQuery->active())
            ->with([
                'product' => function ($productQuery) {
                    $productQuery
                        ->select([
                            'id', 'brand_id', 'name', 'slug', 'price',
                            'discount', 'start_discount', 'end_discount',
                            'manage_stock', 'quantity', 'available_in_stock',
                            'status', 'created_at',
                        ])
                        ->with('brand:id,name')
                        ->withStorefrontData();
                },
            ])
            ->latest()
            ->get();
    }

    public function removeFromWishlist($wishlistItemId): void
    {
        $wishlistItem = Wishlist::query()
            ->where('id', $wishlistItemId)
            ->where('user_id', auth()->id())
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $this->wishlistItems = $this->wishlistItems->where('id', '!=', $wishlistItemId);

            $this->dispatch('wishlist_item_removed');
        }
    }
};
?>

<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <style>
        .wishlist-page-heading {
            background: #fffafe;
            padding: 5rem 0;
            text-align: center;
        }

        .wishlist-page-heading h1 {
            font-size: 3.6rem;
            margin: 0 0 1rem;
        }

        .wishlist-page-heading p {
            color: #797979;
            font-size: 1.5rem;
        }

        .wishlist-page-content {
            padding: 6rem 0;
        }

        .wishlist-table-wrapper {
            background: #fff;
            border: 1px solid #eee4ec;
            border-radius: 1.2rem;
            box-shadow: 0 1.2rem 3rem rgba(35, 37, 50, 0.06);
            overflow: hidden;
        }

        .wishlist-table {
            border-collapse: collapse;
            margin: 0;
            width: 100%;
        }

        .wishlist-table th {
            background: #fff6fc;
            color: #232532;
            font-size: 1.4rem;
            font-weight: 700;
            padding: 1.8rem 2rem;
            text-align: start;
        }

        .wishlist-table td {
            border-top: 1px solid #f1e8ef;
            color: #565664;
            font-size: 1.4rem;
            padding: 1.8rem 2rem;
            vertical-align: middle;
        }

        .wishlist-product {
            align-items: center;
            display: flex;
            gap: 1.5rem;
            min-width: 24rem;
        }

        .wishlist-product-image {
            align-items: center;
            background: #fffafe;
            border-radius: 0.8rem;
            display: flex;
            flex: 0 0 8rem;
            height: 8rem;
            justify-content: center;
            overflow: hidden;
            width: 8rem;
        }

        .wishlist-product-image img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .wishlist-product-name {
            color: #232532;
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.5;
        }

        .wishlist-product-brand {
            color: #8a8994;
            font-size: 1.2rem;
            margin-top: 0.4rem;
        }

        .wishlist-price {
            color: #ae1c9a;
            font-weight: 700;
            white-space: nowrap;
        }

        .wishlist-price-prefix {
            color: #797979;
            display: block;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .wishlist-old-price {
            color: #999;
            font-size: 1.2rem;
            margin-inline-end: 0.5rem;
            text-decoration: line-through;
        }

        .wishlist-stock {
            background: #eaf8ef;
            border-radius: 2rem;
            color: #238636;
            display: inline-flex;
            font-size: 1.2rem;
            font-weight: 600;
            padding: 0.6rem 1.1rem;
            white-space: nowrap;
        }

        .wishlist-stock.is-unavailable {
            background: #fff0f0;
            color: #c62828;
        }

        .wishlist-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .wishlist-view-button,
        .wishlist-delete-button {
            border-radius: 0.7rem;
            display: inline-flex;
            font-size: 1.2rem;
            font-weight: 600;
            justify-content: center;
            padding: 0.8rem 1.1rem;
            white-space: nowrap;
        }

        .wishlist-view-button {
            background: #ae1c9a;
            color: #fff;
        }

        .wishlist-view-button:hover {
            color: #fff;
        }

        .wishlist-delete-button {
            background: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .wishlist-empty {
            background: #fff;
            border: 1px dashed #dfcddd;
            border-radius: 1.2rem;
            padding: 5rem 2rem;
            text-align: center;
        }

        .wishlist-empty h2 {
            font-size: 2.4rem;
            margin-bottom: 1rem;
        }

        .wishlist-empty p {
            font-size: 1.4rem;
        }

        @media (max-width: 767px) {
            .wishlist-page-heading {
                padding: 3rem 0;
            }

            .wishlist-page-heading h1 {
                font-size: 2.8rem;
            }

            .wishlist-page-content {
                padding: 3.5rem 0;
            }

            .wishlist-table-wrapper {
                background: transparent;
                border: 0;
                box-shadow: none;
                overflow: visible;
            }

            .wishlist-table,
            .wishlist-table tbody,
            .wishlist-table tr,
            .wishlist-table td {
                display: block;
                width: 100%;
            }

            .wishlist-table thead {
                display: none;
            }

            .wishlist-table tr {
                background: #fff;
                border: 1px solid #eee4ec;
                border-radius: 1.2rem;
                box-shadow: 0 1rem 2.5rem rgba(35, 37, 50, 0.05);
                margin-bottom: 1.5rem;
                overflow: hidden;
            }

            .wishlist-table td {
                align-items: center;
                border-top: 1px solid #f1e8ef;
                display: flex;
                justify-content: space-between;
                padding: 1.3rem 1.5rem;
                text-align: end;
            }

            .wishlist-table td:first-child {
                border-top: 0;
                display: block;
                text-align: start;
            }

            .wishlist-table td:not(:first-child)::before {
                color: #232532;
                content: attr(data-label);
                font-weight: 700;
                margin-inline-end: 1rem;
                text-align: start;
            }

            .wishlist-product {
                min-width: 0;
            }

            .wishlist-actions {
                justify-content: flex-end;
            }
        }
    </style>

    <section class="wishlist-page-heading">
        <div class="container">
            <h1>{{ __('website.wishlist_title') }}</h1>
            <p>{{ __('website.wishlist_subtitle') }}</p>
        </div>
    </section>

    <section class="wishlist-page-content">
        <div class="container">
            @if ($wishlistItems->isNotEmpty())
                <div class="wishlist-table-wrapper">
                    <table class="wishlist-table">
                        <thead>
                            <tr>
                                <th>{{ __('website.wishlist_product') }}</th>
                                <th>{{ __('website.wishlist_price') }}</th>
                                <th>{{ __('website.wishlist_stock') }}</th>
                                <th>{{ __('website.wishlist_actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($wishlistItems as $wishlistItem)
                                @php
                                    $product = $wishlistItem->product;
                                    $mainImage = $product->images->first();
                                @endphp

                                <tr wire:key="wishlist-row-{{ $wishlistItem->id }}">
                                    <td>
                                        <div class="wishlist-product">
                                            <a
                                                href="{{ route('website.products.show', $product->slug) }}"
                                                class="wishlist-product-image"
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
                                                    class="wishlist-product-name"
                                                >
                                                    {{ $product->name }}
                                                </a>
                                                <p class="wishlist-product-brand">
                                                    {{ $product->brand?->name ?? __('website.no_brand') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="{{ __('website.wishlist_price') }}">
                                        <div class="wishlist-price">
                                            @if ($product->has_variants)
                                                <span class="wishlist-price-prefix">
                                                    {{ __('website.starting_from') }}
                                                </span>
                                                {{ number_format((float) ($product->variants_min_price ?? $product->price), 2) }}
                                            @elseif ($product->hasActiveDiscount())
                                                <span class="wishlist-old-price">
                                                    {{ number_format((float) $product->price, 2) }}
                                                </span>
                                                {{ number_format($product->getPriceAfterDiscount(), 2) }}
                                            @else
                                                {{ number_format((float) $product->price, 2) }}
                                            @endif

                                            {{ __('website.currency') }}
                                        </div>
                                    </td>

                                    <td data-label="{{ __('website.wishlist_stock') }}">
                                        <span class="wishlist-stock {{ $product->isInStock() ? '' : 'is-unavailable' }}">
                                            {{ $product->isInStock()
                                                ? __('website.in_stock')
                                                : __('website.out_of_stock') }}
                                        </span>
                                    </td>

                                    <td data-label="{{ __('website.wishlist_actions') }}">
                                        <div class="wishlist-actions">
                                            <a
                                                href="{{ route('website.products.show', $product->slug) }}"
                                                class="wishlist-view-button"
                                            >
                                                {{ __('website.view_details') }}
                                            </a>

                                            <button
                                                type="button"
                                                class="wishlist-delete-button"
                                                aria-label="{{ __('website.wishlist_delete_product', ['product' => $product->name]) }}"
                                                wire:click="removeFromWishlist({{ $wishlistItem->id }})"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ __('website.wishlist_delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="wishlist-empty">
                    <h2>{{ __('website.wishlist_empty') }}</h2>
                    <p>{{ __('website.wishlist_empty_hint') }}</p>
                </div>
            @endif
        </div>
    </section>
</div>
