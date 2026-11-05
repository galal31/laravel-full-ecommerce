@php
    $productImage = $item->images->first()?->image_url;
    $hasDiscount = ! $item->has_variants && $item->hasActiveDiscount();
    $wishlisted = (bool) $item->is_wishlisted;
@endphp
<article class="sh-product-card">
    <div class="sh-product-visual">
        <a class="sh-product-image" href="{{ route('website.products.show', $item->slug) }}" tabindex="-1" aria-hidden="true">
            <img src="{{ $productImage ?? asset('website-assets/assets/images/store-placeholder.svg') }}" data-fallback-src="{{ asset('website-assets/assets/images/store-placeholder.svg') }}" alt="" loading="lazy" decoding="async" width="240" height="240">
        </a>
        @if ($hasDiscount)
            <span class="sh-badge sh-badge-discount">{{ __('website.discount_badge', ['percentage' => number_format((float) $item->discount, 0)]) }}</span>
        @elseif ($isNew ?? false)
            <span class="sh-badge">{{ __('home.new_arrival') }}</span>
        @endif
        <button type="button" class="sh-wishlist {{ $wishlisted ? 'is-active' : '' }}" data-product-id="{{ $item->id }}" data-url="{{ route('website.wishlist.toggle') }}" aria-pressed="{{ $wishlisted ? 'true' : 'false' }}" aria-label="{{ $wishlisted ? __('website.remove_from_wishlist') : __('website.add_to_wishlist') }}">
            @include('website.home-parts.icon', ['name' => 'heart'])
        </button>
    </div>
    <div class="sh-product-body">
        <p class="sh-product-brand">{{ $item->brand?->name ?? $item->category?->name }}</p>
        <h3><a href="{{ route('website.products.show', $item->slug) }}">{{ $item->name }}</a></h3>
        <div class="sh-product-pricing">
            @if ($item->has_variants)
                <span class="sh-price-prefix">{{ __('website.starting_from') }}</span>
            @endif
            <p class="sh-price"><bdi>{{ number_format($item->has_variants ? (float) ($item->variants_min_price ?? $item->price) : $item->getPriceAfterDiscount(), 2) }}</bdi><span>{{ __('website.currency') }}</span></p>
            @if ($hasDiscount)
                <del class="sh-old-price"><bdi>{{ number_format((float) $item->price, 2) }}</bdi> {{ __('website.currency') }}</del>
            @endif
        </div>
        <p class="sh-stock {{ $item->isInStock() ? '' : 'is-unavailable' }}"><span aria-hidden="true"></span>{{ $item->isInStock() ? __('website.in_stock') : __('website.out_of_stock') }}</p>
        <a class="sh-product-button" href="{{ route('website.products.show', $item->slug) }}">{{ $item->has_variants ? __('home.choose_options') : __('website.view_details') }}@include('website.home-parts.icon', ['name' => 'arrow', 'iconClass' => 'sh-directional'])</a>
    </div>
</article>
