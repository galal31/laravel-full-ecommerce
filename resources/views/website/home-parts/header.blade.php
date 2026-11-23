@php($signedIn = auth()->check())

<div class="sh-topbar">
    <div class="sh-container sh-topbar-inner">
        <span>{{ __('home.topbar_note') }}</span>
        <div>
            <a href="{{ route('website.faqs.index') }}">{{ __('home.help') }}</a>
            <span class="sh-topbar-divider" aria-hidden="true"></span>
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                @if ($localeCode !== app()->getLocale())
                    <a lang="{{ $localeCode }}" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">{{ $localeCode === 'ar' ? 'العربية' : 'English' }}</a>
                @endif
            @endforeach
            @if ($signedIn)
                <form method="POST" action="{{ route('website.logout') }}">
                    @csrf
                    <button type="submit">{{ __('site_header.logout') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>
<header class="sh-header">
    <div class="sh-container sh-header-main">
        <a class="sh-logo" href="{{ route('website.home') }}" aria-label="{{ __('home.home_label') }}">
            <img src="{{ asset('website-assets/assets/images/logos/logo.webp') }}" alt="Shopus" width="148" height="44">
        </a>
        <form class="sh-search" action="{{ route('website.home') }}" method="GET" role="search">
            <label class="sh-sr-only" for="store-search">{{ __('home.search_label') }}</label>
            <input id="store-search" type="search" name="q" value="{{ $searchQuery ?? '' }}" maxlength="100" placeholder="{{ __('home.search_placeholder') }}" enterkeyhint="search">
            <button type="submit" aria-label="{{ __('home.search_label') }}">@include('website.home-parts.icon', ['name' => 'search'])</button>
        </form>
        <div class="sh-account-actions">
            <a class="sh-header-action" href="{{ $signedIn ? route('website.profile') : route('website.login') }}">
                @include('website.home-parts.icon', ['name' => 'user'])
                <span>{{ $signedIn ? __('site_header.my_account') : __('home.account') }}</span>
            </a>
            @livewire('website.wishlist-header-icon')
            @livewire('website.cart-header-icon')
        </div>
    </div>
    <nav class="sh-navigation" aria-label="{{ __('home.shop_navigation') }}">
        <div class="sh-container sh-nav-inner">
            <a class="sh-all-categories" href="{{ route('website.categories.index') }}">@include('website.home-parts.icon', ['name' => 'grid']){{ __('home.all_categories') }}</a>
            @foreach ($headerCategories->take(6) as $category)
                <a href="{{ route('website.categories.products', $category->slug) }}">{{ $category->name }}</a>
            @endforeach
            <a class="sh-nav-new" href="{{ route('website.home') }}#new-arrivals">{{ __('home.fresh_finds') }}@include('website.home-parts.icon', ['name' => 'spark'])</a>
        </div>
    </nav>
</header>
