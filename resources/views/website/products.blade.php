@extends('layouts.website.userlayout')

@section('title', __('website.products'))

@push('styles')
    <style>
        .catalog-products-heading {
            background: #fffafe;
            padding: 5rem 0;
            text-align: center;
        }

        .catalog-products-heading h1 {
            font-size: 3.6rem;
            margin: 0;
        }

        .catalog-products-content {
            padding: 6rem 0 17rem;
        }

        .catalog-product-card {
            background: #fff;
            border: 1px solid transparent;
            border-radius: 1.6rem;
            box-shadow: 0 1rem 3rem rgba(35, 37, 50, 0.08);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .catalog-product-card:hover {
            border-color: rgba(174, 28, 154, 0.35);
            box-shadow: 0 1.6rem 3.8rem rgba(35, 37, 50, 0.13);
            transform: translateY(-0.6rem);
        }

        .catalog-product-image {
            background: linear-gradient(145deg, #fff7fd, #f7f3f7);
            height: 26rem;
            overflow: hidden;
            padding: 2rem;
            position: relative;
        }

        .catalog-product-image img {
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            width: 100%;
        }

        .catalog-product-card:hover .catalog-product-image img {
            transform: scale(1.04);
        }

        .catalog-discount-badge {
            background: #ae1c9a;
            border-radius: 2rem;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            inset-inline-start: 1.5rem;
            padding: 0.7rem 1.2rem;
            position: absolute;
            top: 1.5rem;
            z-index: 1;
        }

        .catalog-wishlist-button {
            align-items: center;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0.6rem 1.8rem rgba(35, 37, 50, 0.12);
            color: #797979;
            display: flex;
            height: 4.2rem;
            inset-inline-end: 1.5rem;
            justify-content: center;
            position: absolute;
            top: 1.5rem;
            transition: color 0.2s ease, transform 0.2s ease;
            width: 4.2rem;
            z-index: 2;
        }

        .catalog-wishlist-button:hover {
            color: #ae1c9a;
            transform: scale(1.08);
        }

        .catalog-wishlist-button:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .catalog-wishlist-button svg {
            fill: transparent;
            stroke: currentColor;
            stroke-width: 1.8;
            transition: fill 0.2s ease;
        }

        .catalog-wishlist-button.is-active {
            color: #ae1c9a;
        }

        .catalog-wishlist-button.is-active svg {
            fill: currentColor;
        }

        .catalog-product-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 2rem;
        }

        .catalog-product-brand {
            color: #797979;
            font-size: 1.3rem;
            margin-bottom: 0.6rem;
        }

        .catalog-product-name {
            color: #232532;
            display: -webkit-box;
            font-size: 1.8rem;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 1.2rem;
            min-height: 5.4rem;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .catalog-product-price {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .catalog-product-price .price-cut {
            color: #797979;
            font-size: 1.5rem;
            text-decoration: line-through;
        }

        .catalog-product-price .new-price,
        .catalog-product-price .regular-price {
            color: #ae1c9a;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .catalog-price-prefix {
            color: #797979;
            font-size: 1.3rem;
        }

        .catalog-product-options {
            color: #797979;
            font-size: 1.3rem;
            margin-top: 0.7rem;
        }

        .catalog-discount-expiry {
            color: #ae1c9a;
            font-size: 1.2rem;
            margin-top: 0.8rem;
        }

        .catalog-product-stock {
            align-items: center;
            border-top: 1px solid #f0e8ee;
            color: #238636;
            display: flex;
            font-size: 1.3rem;
            gap: 0.7rem;
            margin-top: auto;
            padding-top: 1.5rem;
        }

        .catalog-product-stock::before {
            background: currentColor;
            border-radius: 50%;
            content: "";
            height: 0.8rem;
            width: 0.8rem;
        }

        .catalog-product-stock.is-unavailable {
            color: #c0392b;
        }

        .catalog-details-button {
            background: #ae1c9a;
            border: 1px solid #ae1c9a;
            border-radius: 0.8rem;
            color: #fff;
            display: block;
            font-size: 1.4rem;
            font-weight: 600;
            margin-top: 1.5rem;
            padding: 1.1rem 1.5rem;
            text-align: center;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .catalog-details-button:hover {
            background: #fff;
            color: #ae1c9a;
        }

        .catalog-action-message {
            background: #232532;
            border-radius: 0.8rem;
            bottom: 2rem;
            color: #fff;
            font-size: 1.4rem;
            left: 50%;
            opacity: 0;
            padding: 1.2rem 2rem;
            pointer-events: none;
            position: fixed;
            transform: translate(-50%, 2rem);
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 1050;
        }

        .catalog-action-message.is-visible {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        .catalog-product-variants {
            color: #ae1c9a;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .catalog-results-summary {
            color: #797979;
            font-size: 1.5rem;
            margin: 3rem 0 1.5rem;
            text-align: center;
        }

        .catalog-pagination {
            display: flex;
            justify-content: center;
        }

        .catalog-pagination .pagination {
            margin: 0;
        }

        .catalog-pagination .page-link {
            color: #ae1c9a;
            font-size: 1.4rem;
            padding: 0.8rem 1.2rem;
        }

        .catalog-pagination .active > .page-link {
            background: #ae1c9a;
            border-color: #ae1c9a;
            color: #fff;
        }

        .catalog-products-empty {
            color: #797979;
            font-size: 1.6rem;
            margin: 0;
            padding: 4rem 0;
            text-align: center;
        }

        @media (max-width: 767px) {
            .catalog-products-heading {
                padding: 3rem 0;
            }

            .catalog-products-heading h1 {
                font-size: 2.8rem;
            }

            .catalog-products-content {
                padding: 3.5rem 0 15rem;
            }

            .catalog-product-image {
                height: 24rem;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="catalog-products-heading">
            <div class="container">
                <h1>{{ __('website.products') }}</h1>
            </div>
        </section>

        <section class="catalog-products-content">
            <div class="container">
                @if ($products->isNotEmpty())
                    <div class="row g-4">
                        @foreach ($products as $item)
                            @php
                                $mainImage = $item->images->first();
                            @endphp

                            <div class="col-xl-3 col-lg-4 col-sm-6">
                                <article class="catalog-product-card">
                                    <div class="catalog-product-image">
                                        @if ($item->hasActiveDiscount())
                                            <span class="catalog-discount-badge">
                                                {{ __('website.discount_badge', [
                                                    'percentage' => number_format((float) $item->discount, 0),
                                                ]) }}
                                            </span>
                                        @endif

                                        <button
                                            type="button"
                                            class="catalog-wishlist-button wishlist-toggle {{ $item->is_wishlisted ? 'is-active' : '' }}"
                                            data-product-id="{{ $item->id }}"
                                            data-url="{{ route('website.wishlist.toggle') }}"
                                            data-login-url="{{ route('website.login') }}"
                                            aria-pressed="{{ $item->is_wishlisted ? 'true' : 'false' }}"
                                            aria-label="{{ $item->is_wishlisted
                                                ? __('website.remove_from_wishlist')
                                                : __('website.add_to_wishlist') }}"
                                            title="{{ $item->is_wishlisted
                                                ? __('website.remove_from_wishlist')
                                                : __('website.add_to_wishlist') }}"
                                        >
                                            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/>
                                            </svg>
                                        </button>

                                        <img
                                            src="{{ $mainImage?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                            alt="{{ $item->name }}"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </div>

                                    <div class="catalog-product-body">
                                        <p class="catalog-product-brand">
                                            {{ $item->brand?->name ?? __('website.no_brand') }}
                                        </p>

                                        <h2 class="catalog-product-name">{{ $item->name }}</h2>

                                        @if ($item->has_variants)
                                            <div class="catalog-product-price">
                                                <span class="catalog-price-prefix">
                                                    {{ __('website.starting_from') }}
                                                </span>
                                                <span class="catalog-product-variants">
                                                    {{ number_format(
                                                        (float) ($item->variants_min_price ?? $item->price),
                                                        2
                                                    ) }}
                                                    {{ __('website.currency') }}
                                                </span>
                                            </div>
                                            <p class="catalog-product-options">
                                                {{ __('website.options_count', [
                                                    'count' => $item->variants_count,
                                                ]) }}
                                            </p>
                                        @else
                                            <div class="catalog-product-price">
                                                @if ($item->hasActiveDiscount())
                                                    <span class="price-cut">
                                                        {{ number_format((float) $item->price, 2) }}
                                                    </span>
                                                    <span class="new-price">
                                                        {{ number_format($item->getPriceAfterDiscount(), 2) }}
                                                        {{ __('website.currency') }}
                                                    </span>
                                                @else
                                                    <span class="regular-price">
                                                        {{ number_format((float) $item->price, 2) }}
                                                        {{ __('website.currency') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        @include('website.partials.discount-expiry', [
                                            'product' => $item,
                                            'className' => 'catalog-discount-expiry',
                                        ])

                                        <p class="catalog-product-stock {{ $item->isInStock() ? '' : 'is-unavailable' }}">
                                            {{ $item->isInStock()
                                                ? __('website.in_stock')
                                                : __('website.out_of_stock') }}
                                        </p>

                                        <a
                                            href="{{ route('website.products.show', $item->slug) }}"
                                            class="catalog-details-button"
                                        >
                                            {{ __('website.view_details') }}
                                        </a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <p class="catalog-results-summary">
                        {{ __('website.showing_results', [
                            'from' => $products->firstItem(),
                            'to' => $products->lastItem(),
                            'total' => $products->total(),
                        ]) }}
                    </p>

                    <div class="catalog-pagination">
                        {{ $products->links() }}
                    </div>
                @else
                    <p class="catalog-products-empty">{{ __('website.no_products') }}</p>
                @endif
            </div>
        </section>

        <div class="catalog-action-message" role="status" aria-live="polite"></div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const messageBox = $('.catalog-action-message');
            let messageTimer;

            function showMessage(message) {
                messageBox.text(message).addClass('is-visible');

                clearTimeout(messageTimer);
                messageTimer = setTimeout(function () {
                    messageBox.removeClass('is-visible');
                }, 2200);
            }

            $('.wishlist-toggle').on('click', function () {
                const button = $(this);

                button.prop('disabled', true);

                $.ajax({
                    url: button.attr('data-url'),
                    type: 'POST',
                    data: {
                        product_id: button.attr('data-product-id'),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (data) {
                        button.toggleClass('is-active', data.added);
                        button.attr('aria-pressed', data.added);
                        button.attr(
                            'aria-label',
                            data.added
                                ? @json(__('website.remove_from_wishlist'))
                                : @json(__('website.add_to_wishlist'))
                        );
                        button.attr('title', button.attr('aria-label'));

                        if (window.Livewire) {
                            Livewire.dispatch(
                                data.added ? 'wishlist_item_added' : 'wishlist_item_removed'
                            );
                        }

                        showMessage(data.message);
                    },
                    error: function (response) {
                        if (response.status === 401) {
                            window.location.href = button.attr('data-login-url');
                            return;
                        }

                        showMessage(@json(__('website.request_failed')));
                    },
                    complete: function () {
                        button.prop('disabled', false);
                    },
                });
            });
        });
    </script>
@endpush
