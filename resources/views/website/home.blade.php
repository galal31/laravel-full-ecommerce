@extends('layouts.website.storefront')
@section('title', __('home.page_title'))

@section('content')
<div class="sh-container sh-page-content">
    @if ($errors->has('q'))
        <p class="sh-form-error" role="alert">{{ $errors->first('q') }}</p>
    @endif

    @if ($searchQuery !== '')
        <section class="sh-section sh-results" aria-labelledby="search-heading">
            <div class="sh-section-heading">
                <div><p class="sh-eyebrow">{{ __('home.search_label') }}</p><h1 id="search-heading">{{ __('home.search_results', ['query' => $searchQuery]) }}</h1></div>
                <a class="sh-text-link" href="{{ route('website.home') }}">{{ __('home.clear_search') }} @include('website.home-parts.icon', ['name' => 'close'])</a>
            </div>
            @if ($searchProducts->isNotEmpty())
                <p class="sh-section-description">{{ __('home.search_limit_note') }}</p>
                <div class="sh-products-grid">@foreach ($searchProducts as $item) @include('website.home-parts.product-card', ['item' => $item]) @endforeach</div>
            @else
                <div class="sh-empty">@include('website.home-parts.icon', ['name' => 'search'])<h2>{{ __('home.no_search_results') }}</h2><p>{{ __('home.search_again') }}</p><a class="sh-button" href="{{ route('website.categories.index') }}">{{ __('home.all_categories') }}</a></div>
            @endif
        </section>
    @else
        <section class="sh-hero" aria-labelledby="hero-heading">
            <div class="sh-hero-main">
                <div class="sh-hero-copy">
                    <p class="sh-eyebrow">{{ __('home.hero_eyebrow') }}</p>
                    <h1 id="hero-heading">{{ __('home.hero_title_first') }}<br><span>{{ __('home.hero_title_second') }}</span></h1>
                    <p>{{ __('home.hero_description') }}</p>
                    <a class="sh-button" href="#new-arrivals">{{ __('home.discover_new') }} @include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a>
                </div>
                <div class="sh-hero-art">
                    @if ($sliders->isNotEmpty())
                        @foreach ($sliders as $slider)
                            <img class="sh-hero-slide" src="{{ $slider->file_name }}" data-fallback-src="{{ asset('website-assets/assets/images/homepage-one/hero-slider-one.webp') }}" alt="{{ $slider->note }}" width="920" height="550" @if (! $loop->first) hidden @endif fetchpriority="{{ $loop->first ? 'high' : 'low' }}" @if (! $loop->first) loading="lazy" @endif>
                        @endforeach
                    @else
                        <img src="{{ asset('website-assets/assets/images/homepage-one/hero-slider-one.webp') }}" alt="" width="920" height="550" fetchpriority="high">
                    @endif
                    @if ($sliders->count() > 1)
                        <div class="sh-slider-controls" role="group" aria-label="{{ __('home.hero_navigation') }}">
                            @foreach ($sliders as $slider)
                                <button type="button" data-slide="{{ $loop->index }}" aria-label="{{ __('home.show_slide', ['number' => $loop->iteration]) }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <aside class="sh-hero-side">
                <div class="sh-side-label">@include('website.home-parts.icon', ['name' => 'bag'])<span>SHOPUS / {{ __('home.edit') }}</span></div>
                <div><p>{{ __('home.side_eyebrow') }}</p><h2>{{ __('home.side_title') }}</h2></div>
                <div class="sh-side-category-list">
                    @foreach ($categories->take(3) as $category)
                        <a href="{{ route('website.categories.products', $category->slug) }}">{{ $category->name }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a>
                    @endforeach
                </div>
                <a class="sh-side-link" href="{{ route('website.categories.index') }}">{{ __('home.explore_categories') }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a>
            </aside>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section class="sh-category-section" aria-labelledby="categories-heading">
            <div class="sh-section-heading"><h2 id="categories-heading">{{ __('home.shop_by_category') }}</h2><a class="sh-text-link" href="{{ route('website.categories.index') }}">{{ __('home.view_all') }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a></div>
            <div class="sh-category-grid">
                @foreach ($categories as $category)
                    <a class="sh-category" href="{{ route('website.categories.products', $category->slug) }}"><span class="sh-category-image"><img src="{{ $category->icon }}" data-fallback-src="{{ asset('website-assets/assets/images/store-placeholder.svg') }}" alt="" width="88" height="88" loading="lazy"></span><span>{{ $category->name }}</span></a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($searchQuery === '')
        @if ($discountEndingTodayProducts->isNotEmpty())
            <section class="sh-section sh-deals" id="today-deals" aria-labelledby="deals-heading">
                <div class="sh-section-heading">
                    <div class="sh-deal-title">@include('website.home-parts.icon', ['name' => 'spark'])<div><p class="sh-eyebrow">{{ __('home.ending_deals_eyebrow') }}</p><h2 id="deals-heading">{{ __('home.ending_deals') }}</h2></div></div>
                    <span class="sh-deal-note">{{ __('home.ending_today') }}</span>
                </div>
                <div class="sh-products-grid">@foreach ($discountEndingTodayProducts as $item) @include('website.home-parts.product-card', ['item' => $item]) @endforeach</div>
            </section>
        @endif

        <section class="sh-section" id="new-arrivals" aria-labelledby="new-heading">
            <div class="sh-section-heading"><div><p class="sh-eyebrow">{{ __('home.fresh_finds_eyebrow') }}</p><h2 id="new-heading">{{ __('home.fresh_finds') }}</h2></div><a class="sh-text-link" href="{{ route('website.categories.index') }}">{{ __('home.explore_more') }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a></div>
            @if ($newestProducts->isNotEmpty())
                <div class="sh-products-grid">@foreach ($newestProducts as $item) @include('website.home-parts.product-card', ['item' => $item, 'isNew' => true]) @endforeach</div>
            @else
                <div class="sh-empty"><h3>{{ __('home.no_new_products') }}</h3><a class="sh-text-link" href="{{ route('website.categories.index') }}">{{ __('home.all_categories') }}</a></div>
            @endif
        </section>

        @if ($brands->isNotEmpty())
            <section class="sh-section sh-brands-section" aria-labelledby="brands-heading">
                <div class="sh-section-heading"><div><p class="sh-eyebrow">{{ __('home.brands_eyebrow') }}</p><h2 id="brands-heading">{{ __('home.featured_brands') }}</h2></div><a class="sh-text-link" href="{{ route('website.brands.index') }}">{{ __('home.all_brands') }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a></div>
                <div class="sh-brand-grid">
                    @foreach ($brands as $brand)
                        <a class="sh-brand" href="{{ route('website.brands.products', $brand->slug) }}"><img src="{{ $brand->logo }}" data-fallback-src="{{ asset('website-assets/assets/images/store-placeholder.svg') }}" alt="" width="110" height="54" loading="lazy"><span>{{ $brand->name }}</span></a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</div>
@endsection
