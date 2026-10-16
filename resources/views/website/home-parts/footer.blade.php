<footer class="sh-footer">
    <div class="sh-container sh-footer-main">
        <div class="sh-footer-brand">
            <a href="{{ route('website.home') }}"><img src="{{ asset('website-assets/assets/images/logos/logo.webp') }}" alt="Shopus" width="136" height="40" loading="lazy"></a>
            <p>{{ __('home.store_description') }}</p>
        </div>
        <div><h2>{{ __('home.discover') }}</h2><a href="{{ route('website.categories.index') }}">{{ __('home.all_categories') }}</a><a href="{{ route('website.brands.index') }}">{{ __('home.all_brands') }}</a><a href="{{ route('website.home') }}#new-arrivals">{{ __('home.fresh_finds') }}</a></div>
        <div><h2>{{ __('home.your_account') }}</h2><a href="{{ route('website.cart.index') }}">{{ __('home.cart') }}</a><a href="{{ route('website.wishlist.index') }}">{{ __('home.wishlist') }}</a><a href="{{ route('website.faqs.index') }}">{{ __('home.help') }}</a></div>
        @if ($dynamicPages->isNotEmpty())
            <div><h2>{{ __('home.about_store') }}</h2>@foreach ($dynamicPages as $page)<a href="{{ route('website.pages.show', $page->slug) }}">{{ $page->title }}</a>@endforeach</div>
        @endif
    </div>
    <div class="sh-container sh-footer-bottom"><span>© {{ date('Y') }} Shopus. {{ __('home.rights') }}</span><a href="#main-content">{{ __('home.back_top') }} ↑</a></div>
</footer>
