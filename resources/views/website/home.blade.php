@extends('layouts.website.userlayout')

@section('title', __('home.page_title'))

@push('styles')
    <style>
        .home-catalog-image {
            display: block;
            height: 8rem;
            object-fit: contain;
            width: 8rem;
        }

        .home-brand-image {
            height: 7rem;
            max-width: 13rem;
            object-fit: contain;
            width: 100%;
        }

        .home-catalog-name {
            color: #232532;
            line-height: 1.5;
            margin: 0;
            text-align: center;
        }

        .home-catalog-empty {
            color: #797979;
            font-size: 1.6rem;
            margin: 0;
            padding: 3rem 0;
            text-align: center;
        }

        .fresh-finds-section {
            background:
                radial-gradient(circle at 10% 15%, rgba(174, 28, 154, 0.08), transparent 28rem),
                linear-gradient(180deg, #fff 0%, #fff7fc 100%);
            overflow: hidden;
            padding: 7rem 0 17rem;
            position: relative;
        }

        .ending-deals-section {
            background: #232532;
            border-bottom: 0.4rem solid #f4b740;
            border-top: 0.4rem solid #f4b740;
            padding: 7rem 0 9rem;
        }

        .ending-deals-heading {
            align-items: center;
        }

        .ending-deals-icon {
            align-items: center;
            background: rgba(244, 183, 64, 0.12);
            border: 1px solid rgba(244, 183, 64, 0.45);
            border-radius: 50%;
            color: #f4b740;
            display: inline-flex;
            flex: 0 0 5.6rem;
            height: 5.6rem;
            justify-content: center;
            width: 5.6rem;
        }

        .ending-deals-section .fresh-finds-title,
        .ending-deals-section .fresh-finds-subtitle {
            color: #fff;
        }

        .ending-deals-section .fresh-finds-eyebrow {
            color: #f4b740;
        }

        .ending-deals-section .fresh-find-card {
            border-color: #3d4050;
        }

        .fresh-finds-heading {
            align-items: end;
            display: flex;
            justify-content: space-between;
            margin-bottom: 3.5rem;
        }

        .fresh-finds-eyebrow {
            color: #ae1c9a;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
        }

        .fresh-finds-title {
            color: #232532;
            font-size: clamp(3rem, 4vw, 4.6rem);
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .fresh-finds-subtitle {
            color: #797979;
            font-size: 1.6rem;
            line-height: 1.8;
            margin: 0;
            max-width: 58rem;
        }

        .fresh-find-card {
            background: #fff;
            border: 1px solid #f0e4ec;
            border-radius: 2rem;
            box-shadow: 0 1.2rem 3rem rgba(62, 26, 55, 0.08);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
        }

        .fresh-find-card:hover {
            border-color: rgba(174, 28, 154, 0.35);
            box-shadow: 0 1.8rem 4rem rgba(62, 26, 55, 0.14);
            transform: translateY(-0.6rem);
        }

        .fresh-find-image {
            background: linear-gradient(145deg, #fff9fd, #f7f1f6);
            display: block;
            height: 25rem;
            overflow: hidden;
            padding: 2.2rem;
            position: relative;
        }

        .fresh-find-image img {
            height: 100%;
            object-fit: contain;
            transition: transform 0.35s ease;
            width: 100%;
        }

        .fresh-find-card:hover .fresh-find-image img {
            transform: scale(1.05);
        }

        .fresh-find-badge,
        .fresh-find-discount {
            border-radius: 5rem;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 0.7rem 1.1rem;
            position: absolute;
            top: 1.4rem;
            z-index: 1;
        }

        .fresh-find-badge {
            background: #232532;
            inset-inline-start: 1.4rem;
        }

        .fresh-find-discount {
            background: #ae1c9a;
            inset-inline-end: 1.4rem;
        }

        .fresh-find-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 2rem;
        }

        .fresh-find-brand {
            color: #ae1c9a;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.7rem;
            text-transform: uppercase;
        }

        .fresh-find-name {
            color: #232532;
            display: -webkit-box;
            font-size: 1.8rem;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 1.4rem;
            min-height: 5.4rem;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .fresh-find-price {
            align-items: baseline;
            color: #ae1c9a;
            display: flex;
            flex-wrap: wrap;
            font-size: 1.8rem;
            font-weight: 700;
            gap: 0.7rem;
            margin-bottom: 1.5rem;
        }

        .fresh-find-price-prefix,
        .fresh-find-old-price {
            color: #797979;
            font-size: 1.3rem;
            font-weight: 400;
        }

        .fresh-find-old-price {
            text-decoration: line-through;
        }

        .fresh-find-footer {
            align-items: center;
            border-top: 1px solid #f0e8ee;
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 1.5rem;
        }

        .fresh-find-stock {
            align-items: center;
            color: #238636;
            display: flex;
            font-size: 1.2rem;
            gap: 0.6rem;
            margin: 0;
        }

        .fresh-find-stock::before {
            background: currentColor;
            border-radius: 50%;
            content: "";
            height: 0.7rem;
            width: 0.7rem;
        }

        .fresh-find-stock.is-unavailable {
            color: #c0392b;
        }

        .fresh-find-link {
            align-items: center;
            color: #232532;
            display: inline-flex;
            font-size: 1.3rem;
            font-weight: 700;
            gap: 0.7rem;
        }

        .fresh-find-link:hover {
            color: #ae1c9a;
        }

        [dir="rtl"] .fresh-find-link svg {
            transform: rotate(180deg);
        }

        @media (max-width: 767px) {
            .fresh-finds-section {
                padding: 5rem 0 15rem;
            }

            .fresh-finds-heading {
                margin-bottom: 2.5rem;
            }

            .fresh-find-image {
                height: 22rem;
            }

            .ending-deals-section {
                padding: 5rem 0 7rem;
            }

            .ending-deals-heading {
                align-items: flex-start;
                gap: 2rem;
            }

            .ending-deals-icon {
                flex-basis: 4.6rem;
                height: 4.6rem;
                width: 4.6rem;
            }
        }
    </style>
@endpush

@section('content')
    <section id="hero" class="hero">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper hero-wrapper">
                @foreach ($sliders as $slider)
                    <div class="swiper-slide hero-slider-one"
                         style="background-image: url('{{ $slider->file_name }}'); background-size: cover; background-position: center;">
                        <div class="container">
                            <div class="col-lg-6">
                                <div class="wrapper-section" data-aos="fade-up">
                                    <div class="wrapper-info">
                                        <h5 class="wrapper-subtitle">
                                            {{ $slider->note }}
                                        </h5>

                                        <h1 class="wrapper-details">
                                            {{ $slider->note }}
                                        </h1>

                                        <a href="{{ $slider->link ?? '#' }}" class="shop-btn">
                                            {{ __('home.shop_now') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="swiper-pagination"></div>
        </div>
    </section>

    <section class="product">
        <div class="container">
            <div class="section-title">
                <h5>{{ __('home.shop_by_category') }}</h5>
                <a class="view" href="{{ route('website.categories.index') }}">
                    {{ __('home.view_all') }}
                </a>
            </div>

            @if ($categories->isNotEmpty())
                <div class="category-section">
                    @foreach ($categories as $category)
                        <a
                            class="product-wrapper"
                            href="{{ route('website.categories.products', $category->slug) }}"
                        >
                            <div class="wrapper-img">
                                <img
                                    class="home-catalog-image"
                                    src="{{ $category->icon }}"
                                    alt="{{ $category->getTranslation('name', app()->getLocale()) }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                            <h3 class="wrapper-details home-catalog-name">
                                {{ $category->getTranslation('name', app()->getLocale()) }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="home-catalog-empty">{{ __('home.no_categories') }}</p>
            @endif
        </div>
    </section>

    <section class="product pt-0">
        <div class="container">
            <div class="section-title">
                <h5>{{ __('home.featured_brands') }}</h5>
                <a class="view" href="{{ route('website.brands.index') }}">
                    {{ __('home.view_all') }}
                </a>
            </div>

            @if ($brands->isNotEmpty())
                <div class="brand-section">
                    @foreach ($brands as $brand)
                        <a
                            class="product-wrapper"
                            href="{{ route('website.brands.products', $brand->slug) }}"
                            title="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                        >
                            <div class="wrapper-img">
                                <img
                                    class="home-brand-image"
                                    src="{{ $brand->logo }}"
                                    alt="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="home-catalog-empty">{{ __('home.no_brands') }}</p>
            @endif
        </div>
    </section>

    <section
        class="fresh-finds-section"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    >
        <div class="container">
            <div class="fresh-finds-heading">
                <div>
                    <p class="fresh-finds-eyebrow">{{ __('home.fresh_finds_eyebrow') }}</p>
                    <h2 class="fresh-finds-title">{{ __('home.fresh_finds') }}</h2>
                    <p class="fresh-finds-subtitle">{{ __('home.fresh_finds_subtitle') }}</p>
                </div>
            </div>

            @if ($newestProducts->isNotEmpty())
                <div class="row g-4">
                    @foreach ($newestProducts as $item)
                        @php
                            $mainImage = $item->images->first();
                        @endphp

                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <article class="fresh-find-card">
                                <a
                                    href="{{ route('website.products.show', $item->slug) }}"
                                    class="fresh-find-image"
                                >
                                    <span class="fresh-find-badge">{{ __('home.new_arrival') }}</span>

                                    @if ($item->hasActiveDiscount())
                                        <span class="fresh-find-discount">
                                            {{ __('website.discount_badge', [
                                                'percentage' => number_format((float) $item->discount, 0),
                                            ]) }}
                                        </span>
                                    @endif

                                    <img
                                        src="{{ $mainImage?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>

                                <div class="fresh-find-body">
                                    <p class="fresh-find-brand">
                                        {{ $item->brand?->name ?? __('website.no_brand') }}
                                    </p>

                                    <h3 class="fresh-find-name">{{ $item->name }}</h3>

                                    <div class="fresh-find-price">
                                        @if ($item->has_variants)
                                            <span class="fresh-find-price-prefix">
                                                {{ __('website.starting_from') }}
                                            </span>
                                            <span>
                                                {{ number_format(
                                                    (float) ($item->variants_min_price ?? $item->price),
                                                    2
                                                ) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        @elseif ($item->hasActiveDiscount())
                                            <span class="fresh-find-old-price">
                                                {{ number_format((float) $item->price, 2) }}
                                            </span>
                                            <span>
                                                {{ number_format($item->getPriceAfterDiscount(), 2) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        @else
                                            <span>
                                                {{ number_format((float) $item->price, 2) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="fresh-find-footer">
                                        <p class="fresh-find-stock {{ $item->isInStock() ? '' : 'is-unavailable' }}">
                                            {{ $item->isInStock()
                                                ? __('website.in_stock')
                                                : __('website.out_of_stock') }}
                                        </p>

                                        <a
                                            href="{{ route('website.products.show', $item->slug) }}"
                                            class="fresh-find-link"
                                        >
                                            {{ __('website.view_details') }}
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="home-catalog-empty">{{ __('home.no_new_products') }}</p>
            @endif
        </div>
    </section>

    <section
        class="fresh-finds-section ending-deals-section"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    >
        <div class="container">
            <div class="fresh-finds-heading ending-deals-heading">
                <div>
                    <p class="fresh-finds-eyebrow">{{ __('home.ending_deals_eyebrow') }}</p>
                    <h2 class="fresh-finds-title">{{ __('home.ending_deals') }}</h2>
                    <p class="fresh-finds-subtitle">{{ __('home.ending_deals_subtitle') }}</p>
                </div>

                <span class="ending-deals-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <path d="M13 2 4.5 13H11l-1 9 8.5-11H12l1-9Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>

            @if ($discountEndingTodayProducts->isNotEmpty())
                <div class="row g-4">
                    @foreach ($discountEndingTodayProducts as $item)
                        @php
                            $mainImage = $item->images->first();
                        @endphp

                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <article class="fresh-find-card">
                                <a
                                    href="{{ route('website.products.show', $item->slug) }}"
                                    class="fresh-find-image"
                                >
                                    <span class="fresh-find-badge">{{ __('home.ending_today') }}</span>

                                    <span class="fresh-find-discount">
                                        {{ __('website.discount_badge', [
                                            'percentage' => number_format((float) $item->discount, 0),
                                        ]) }}
                                    </span>

                                    <img
                                        src="{{ $mainImage?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>

                                <div class="fresh-find-body">
                                    <p class="fresh-find-brand">
                                        {{ $item->brand?->name ?? __('website.no_brand') }}
                                    </p>

                                    <h3 class="fresh-find-name">{{ $item->name }}</h3>

                                    <div class="fresh-find-price">
                                        @if ($item->has_variants)
                                            <span class="fresh-find-price-prefix">
                                                {{ __('website.starting_from') }}
                                            </span>
                                            <span>
                                                {{ number_format(
                                                    (float) ($item->variants_min_price ?? $item->price),
                                                    2
                                                ) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        @else
                                            <span class="fresh-find-old-price">
                                                {{ number_format((float) $item->price, 2) }}
                                            </span>
                                            <span>
                                                {{ number_format($item->getPriceAfterDiscount(), 2) }}
                                                {{ __('website.currency') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="fresh-find-footer">
                                        <p class="fresh-find-stock {{ $item->isInStock() ? '' : 'is-unavailable' }}">
                                            {{ $item->isInStock()
                                                ? __('website.in_stock')
                                                : __('website.out_of_stock') }}
                                        </p>

                                        <a
                                            href="{{ route('website.products.show', $item->slug) }}"
                                            class="fresh-find-link"
                                        >
                                            {{ __('website.view_details') }}
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="home-catalog-empty">{{ __('home.no_ending_deals') }}</p>
            @endif
        </div>
    </section>
@endsection
