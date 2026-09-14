@if ($relatedProducts->isNotEmpty())
    <style>
        .related-products-section {
            background: #fffafe;
            padding: 6rem 0 17rem;
        }

        .related-products-title {
            font-size: 3rem;
            margin-bottom: 3rem;
            text-align: center;
        }

        .related-product-card {
            background: #fff;
            border-radius: 1.6rem;
            box-shadow: 0 1rem 3rem rgba(35, 37, 50, 0.08);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .related-product-image {
            background: linear-gradient(145deg, #fff7fd, #f7f3f7);
            display: block;
            height: 24rem;
            padding: 2rem;
        }

        .related-product-image img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .related-product-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 2rem;
        }

        .related-product-brand {
            color: #797979;
            font-size: 1.3rem;
            margin-bottom: 0.6rem;
        }

        .related-product-name {
            color: #232532;
            font-size: 1.8rem;
            line-height: 1.5;
            margin-bottom: 1.2rem;
        }

        .related-product-price {
            color: #ae1c9a;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
        }

        .related-product-old-price {
            color: #797979;
            font-size: 1.4rem;
            margin-inline-end: 0.8rem;
            text-decoration: line-through;
        }

        .related-product-stock {
            color: #238636;
            font-size: 1.3rem;
            margin-top: auto;
        }

        .related-product-stock.is-unavailable {
            color: #c0392b;
        }

        .related-product-link {
            background: #ae1c9a;
            border-radius: 0.8rem;
            color: #fff;
            display: block;
            font-size: 1.4rem;
            margin-top: 1.5rem;
            padding: 1.1rem 1.5rem;
            text-align: center;
        }

        @media (max-width: 767px) {
            .related-products-section {
                padding: 5rem 0 15rem;
            }

            .related-product-image {
                height: 22rem;
            }
        }
    </style>

    <section
        class="related-products-section"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    >
        <div class="container">
            <h2 class="related-products-title">
                {{ __('website.related_products') }}
            </h2>

            <div class="row g-4">
                @foreach ($relatedProducts as $relatedProduct)
                    @php
                        $mainImage = $relatedProduct->images->first();
                    @endphp

                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <article class="related-product-card">
                            <a
                                href="{{ route('website.products.show', $relatedProduct->slug) }}"
                                class="related-product-image"
                            >
                                <img
                                    src="{{ $mainImage?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                    alt="{{ $relatedProduct->name }}"
                                    loading="lazy"
                                >
                            </a>

                            <div class="related-product-body">
                                <p class="related-product-brand">
                                    {{ $relatedProduct->brand?->name ?? __('website.no_brand') }}
                                </p>

                                <h3 class="related-product-name">
                                    {{ $relatedProduct->name }}
                                </h3>

                                <p class="related-product-price">
                                    @if ($relatedProduct->has_variants)
                                        {{ __('website.starting_from') }}
                                        {{ number_format(
                                            (float) ($relatedProduct->variants_min_price ?? $relatedProduct->price),
                                            2
                                        ) }}
                                    @elseif ($relatedProduct->hasActiveDiscount())
                                        <span class="related-product-old-price">
                                            {{ number_format((float) $relatedProduct->price, 2) }}
                                        </span>
                                        {{ number_format($relatedProduct->getPriceAfterDiscount(), 2) }}
                                    @else
                                        {{ number_format((float) $relatedProduct->price, 2) }}
                                    @endif

                                    {{ __('website.currency') }}
                                </p>

                                <p class="related-product-stock {{ $relatedProduct->isInStock() ? '' : 'is-unavailable' }}">
                                    {{ $relatedProduct->isInStock()
                                        ? __('website.in_stock')
                                        : __('website.out_of_stock') }}
                                </p>

                                <a
                                    href="{{ route('website.products.show', $relatedProduct->slug) }}"
                                    class="related-product-link"
                                >
                                    {{ __('website.view_details') }}
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
