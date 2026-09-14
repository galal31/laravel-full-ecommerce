<?php

use App\Services\website\WishlistService;
use Livewire\Component;
use Livewire\Attributes\Computed; // استدعاء السمة الجديدة

new class extends Component
{
    public $product;
    public bool $is_wishlisted = false;
    public string $displayedImage;
    public array $galleryImages = [];
    // تم حذف public array $variantOptions لتقليل حجم الـ Payload

    public ?int $selectedVariantId = null;
    public ?float $selectedVariantPrice = null;
    public ?int $selectedVariantStock = null;
    public bool $hasVariants = false;
    public bool $productIsInStock = false;
    public float $startingPrice = 0;


    public function mount($product): void
    {
        $this->product = $product;
        $this->is_wishlisted = (bool) ($product->is_wishlisted ?? false);
        $this->hasVariants = (bool) $product->has_variants;
        $this->productIsInStock = $product->isInStock();
        $this->startingPrice = (float) $product->variants_min_price;
        // نضيف صور المنتج الأساسية إلى المعرض.
        foreach ($product->images as $image) {
            $this->galleryImages[] = $image->image_url;
        }

        // نضيف صور المتغيرات إلى المعرض فقط (بدون تخزين بيانات المتغيرات)
        foreach ($product->variants as $variant) {
            foreach ($variant->images as $image) {
                $this->galleryImages[] = $image->image_url;
            }
        }

        // نحذف الصور المكررة
        $this->galleryImages = array_values(array_unique($this->galleryImages));

        if (empty($this->galleryImages)) {
            $this->galleryImages[] = asset(
                'website-assets/assets/images/homepage-one/product-img/product-img-1.webp'
            );
        }

        $this->displayedImage = $this->galleryImages[0];
    }

    // هذه الدالة لن تُرسل للواجهة الأمامية، وسيتم استدعاؤها فقط عند الحاجة
    #[Computed]
    public function variantOptions()
    {
        return $this->product->variants->mapWithKeys(function ($variant) {
            $variantImage = $variant->images->first()?->image_url
                ?? $this->galleryImages[0]
                ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp');

            return [
                $variant->id => [
                    'id' => $variant->id,
                    'name' => $variant->attributeValues
                        ->pluck('value')
                        ->filter()
                        ->join(' | '),
                    'price' => (float) $variant->price,
                    'stock' => (int) $variant->stock,
                    'image' => $variantImage,
                ]
            ];
        })->toArray();
    }

    public function selectImage(int $imageIndex): void
    {
        if (! isset($this->galleryImages[$imageIndex])) {
            return;
        }

        $this->displayedImage = $this->galleryImages[$imageIndex];
    }

    public function selectVariant(int $variantId): void
    {
        // استدعاء البيانات المحسوبة
        $options = $this->variantOptions;

        if (! isset($options[$variantId])) {
            return;
        }

        $variant = $options[$variantId];

        if ($variant['stock'] <= 0) {
            return;
        }

        $this->selectedVariantId = $variantId;
        $this->selectedVariantPrice = $variant['price'];
        $this->selectedVariantStock = $variant['stock'];
        $this->displayedImage = $variant['image'];
    }

    public function toggleWishlist(WishlistService $wishlistService): void
    {
        if (! auth()->check()) {
            session()->flash('message', __('website.login_to_add_to_wishlist'));

            return;
        }

        $this->is_wishlisted = $wishlistService->toggle(
            (int) auth()->id(),
            (int) $this->product->id
        );

        $this->dispatch(
            $this->is_wishlisted ? 'wishlist_item_added' : 'wishlist_item_removed'
        );
    }

};
?>

<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <style>
        .product-details-page {
            padding: 6rem 0;
        }

        .product-breadcrumb {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            font-size: 1.4rem;
            gap: 0.8rem;
            margin-bottom: 2rem;
        }

        .product-breadcrumb a {
            color: #ae1c9a;
        }

        .product-breadcrumb span {
            color: #797979;
        }

        .product-details-card {
            background: #fff;
            border-radius: 1.6rem;
            box-shadow: 0 1rem 3rem rgba(35, 37, 50, 0.08);
            overflow: hidden;
            padding: 3rem;
        }

        .product-details-image {
            background: linear-gradient(145deg, #fff7fd, #f7f3f7);
            border-radius: 1.2rem;
            height: 48rem;
            padding: 3rem;
        }

        .product-details-image img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .product-image-thumbnails {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fill, minmax(7rem, 1fr));
            margin-top: 1.2rem;
        }

        .product-image-thumbnail {
            background: #fff;
            border: 1px solid #eee5ec;
            border-radius: 0.8rem;
            height: 8rem;
            padding: 0.7rem;
        }

        .product-image-thumbnail.is-selected {
            border-color: #ae1c9a;
        }

        .product-image-thumbnail img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .product-details-brand {
            color: #ae1c9a;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .product-details-brand a,
        .product-details-meta a {
            color: inherit;
            text-decoration: underline;
            text-underline-offset: 0.3rem;
        }

        .product-details-name {
            font-size: 3.6rem;
            line-height: 1.3;
            margin-bottom: 1.5rem;
        }

        .product-details-description {
            color: #797979;
            font-size: 1.5rem;
            line-height: 1.9;
            margin: 1.8rem 0;
        }

        .product-details-price {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            margin: 2rem 0;
        }

        .product-details-price .current-price {
            color: #ae1c9a;
            font-size: 2.6rem;
            font-weight: 700;
        }

        .product-details-price .old-price {
            color: #797979;
            font-size: 1.7rem;
            text-decoration: line-through;
        }

        .product-details-stock {
            color: #238636;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .product-details-stock.is-unavailable {
            color: #c0392b;
        }

        .product-details-discount-expiry {
            color: #ae1c9a;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .product-details-meta {
            background: #fff7fd;
            border-radius: 0.8rem;
            color: #565664;
            font-size: 1.4rem;
            margin-top: 1.5rem;
            padding: 1.2rem 1.5rem;
        }

        .product-variants-title,
        .product-description-title {
            font-size: 2.4rem;
            margin: 3rem 0 1.5rem;
        }

        .product-variant-item {
            align-items: center;
            background: #fff;
            border: 1px solid #eee5ec;
            border-radius: 0.8rem;
            display: flex;
            flex-wrap: wrap;
            font-size: 1.4rem;
            gap: 1rem;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 1.2rem 1.5rem;
            text-align: inherit;
            transition: border-color 0.2s ease, background 0.2s ease;
            width: 100%;
        }

        .product-variant-item:hover,
        .product-variant-item.is-selected {
            background: #fff7fd;
            border-color: #ae1c9a;
        }

        .product-variant-item:disabled {
            background: #f4f4f4;
            color: #9b9b9b;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .product-variant-values {
            color: #565664;
        }

        .product-variant-price {
            color: #ae1c9a;
            font-weight: 700;
        }

        .product-details-wishlist {
            align-items: center;
            background: #fff;
            border: 1px solid #ae1c9a;
            border-radius: 0.8rem;
            color: #ae1c9a;
            display: inline-flex;
            font-size: 1.4rem;
            font-weight: 600;
            gap: 0.8rem;
            margin-top: 2rem;
            padding: 1.1rem 1.6rem;
        }

        .product-details-wishlist svg {
            fill: transparent;
            stroke: currentColor;
            stroke-width: 1.8;
        }

        .product-details-wishlist.is-active {
            background: #ae1c9a;
            color: #fff;
        }

        .product-details-wishlist.is-active svg {
            fill: currentColor;
        }

        .product-details-cart {
            align-items: center;
            background: #ae1c9a;
            border: 1px solid #ae1c9a;
            border-radius: 0.8rem;
            color: #fff;
            display: inline-flex;
            font-size: 1.4rem;
            font-weight: 600;
            gap: 0.8rem;
            margin-inline-end: 0.8rem;
            margin-top: 2rem;
            padding: 1.1rem 1.6rem;
            transition: background-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .product-details-cart svg {
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
            transition: transform 180ms ease;
        }

        .product-details-cart:hover {
            background: #8f177f;
            box-shadow: 0 0.8rem 1.8rem rgba(174, 28, 154, 0.2);
            color: #fff;
            transform: translateY(-1px);
        }

        .product-details-cart:hover svg {
            transform: translateX(2px);
        }

        .product-details-cart:focus-visible {
            outline: 3px solid rgba(174, 28, 154, 0.25);
            outline-offset: 3px;
        }

        .product-details-wishlist:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .product-details-actions {
            align-items: flex-end;
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: 2rem;
        }

        .product-details-actions .product-details-cart,
        .product-details-actions .product-details-wishlist {
            margin-inline-end: 0;
            margin-top: 0;
        }

        .product-details-quantity {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .product-details-quantity label {
            color: #565664;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .product-details-quantity input {
            border: 1px solid #ddd4db;
            border-radius: 0.8rem;
            color: #232323;
            font-size: 1.4rem;
            height: 4.6rem;
            padding: 0.8rem 1rem;
            text-align: center;
            width: 8rem;
        }

        .product-details-quantity input:focus {
            border-color: #ae1c9a;
            box-shadow: 0 0 0 3px rgba(174, 28, 154, 0.12);
            outline: none;
        }

        .product-details-message {
            color: #238636;
            font-size: 1.3rem;
            margin-top: 1rem;
            min-height: 2rem;
        }

        .product-details-message.is-success {
            color: #238636;
        }

        .product-details-message.is-error {
            color: #c0392b;
        }

        @media (max-width: 767px) {
            .product-details-actions,
            .product-details-quantity,
            .product-details-quantity input,
            .product-details-cart {
                justify-content: center;
                margin-inline-end: 0;
                width: 100%;
            }

            .product-details-page {
                padding: 3.5rem 0 5rem;
            }

            .product-details-card {
                padding: 2rem;
            }

            .product-details-image {
                height: 32rem;
                padding: 2rem;
            }

            .product-image-thumbnails {
                margin-bottom: 2.5rem;
            }

            .product-details-name {
                font-size: 2.8rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .product-details-cart,
            .product-details-cart svg {
                transition: none;
            }
        }
    </style>

    <section class="product-details-page">
        <div class="container">
            <nav class="product-breadcrumb" aria-label="{{ __('website.breadcrumb') }}">
                <a href="{{ route('website.home') }}">{{ __('website.home') }}</a>
                <span>/</span>

                @if ($product->category)
                    <a href="{{ route('website.categories.products', $product->category->slug) }}">
                        {{ $product->category->name }}
                    </a>
                    <span>/</span>
                @endif

                <span aria-current="page">{{ $product->name }}</span>
            </nav>

            <div class="product-details-card">
                <div class="row align-items-start g-5">
                    <div class="col-lg-6">
                        <div class="product-details-image">
                            <img
                                src="{{ $displayedImage }}"
                                alt="{{ $product->name }}"
                            >
                        </div>

                        @if (count($galleryImages) > 1)
                            <div class="product-image-thumbnails">
                                @foreach ($galleryImages as $imageIndex => $image)
                                    <button
                                        type="button"
                                        wire:click="selectImage({{ $imageIndex }})"
                                        class="product-image-thumbnail {{ $displayedImage === $image ? 'is-selected' : '' }}"
                                        aria-label="{{ __('website.show_product_image', [
                                            'number' => $imageIndex + 1,
                                        ]) }}"
                                    >
                                        <img
                                            src="{{ $image }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-6">
                        <p class="product-details-brand">
                            @if ($product->brand)
                                <a href="{{ route('website.brands.products', $product->brand->slug) }}">
                                    {{ $product->brand->name }}
                                </a>
                            @else
                                {{ __('website.no_brand') }}
                            @endif
                        </p>

                        <h1 class="product-details-name">{{ $product->name }}</h1>

                        @if ($product->small_desc)
                            <p class="product-details-description">{{ $product->small_desc }}</p>
                        @endif

                        <div class="product-details-price">
                            @if ($hasVariants)
                                @if ($selectedVariantId === null)
                                    <span>{{ __('website.starting_from') }}</span>
                                @endif
                                <span class="current-price">
                                    {{ number_format(
                                        $selectedVariantPrice ?? $startingPrice,
                                        2
                                    ) }}
                                    {{ __('website.currency') }}
                                </span>
                            @elseif ($product->hasActiveDiscount())
                                <span class="old-price">
                                    {{ number_format((float) $product->price, 2) }}
                                </span>
                                <span class="current-price">
                                    {{ number_format($product->getPriceAfterDiscount(), 2) }}
                                    {{ __('website.currency') }}
                                </span>
                            @else
                                <span class="current-price">
                                    {{ number_format((float) $product->price, 2) }}
                                    {{ __('website.currency') }}
                                </span>
                            @endif
                        </div>

                        @include('website.partials.discount-expiry', [
                            'product' => $product,
                            'className' => 'product-details-discount-expiry',
                        ])

                        <p class="product-details-stock {{ $productIsInStock ? '' : 'is-unavailable' }}">
                            @if ($selectedVariantId !== null)
                                {{ __('website.pieces_available', [
                                    'count' => $selectedVariantStock,
                                ]) }}
                            @else
                                {{ $productIsInStock
                                    ? __('website.in_stock')
                                    : __('website.out_of_stock') }}
                            @endif
                        </p>

                        <p class="product-details-meta">
                            {{ __('website.category') }}:
                            @if ($product->category)
                                <a href="{{ route('website.categories.products', $product->category->slug) }}">
                                    {{ $product->category->name }}
                                </a>
                            @endif
                        </p>

                        @if (! empty($this->variantOptions()))
    <h2 class="product-variants-title">
        {{ __('website.available_options') }}
    </h2>

    @foreach ($this->variantOptions() as $variant)

        <button
            type="button"
            wire:click="selectVariant({{ $variant['id'] }})"
            class="product-variant-item {{ $selectedVariantId === $variant['id'] ? 'is-selected' : '' }}"
            @disabled($variant['stock'] <= 0)
        >
            <span class="product-variant-values">
                {{ $variant['name'] }}
            </span>

            <span class="product-variant-price">
                {{ number_format($variant['price'], 2) }}
                {{ __('website.currency') }}
            </span>

            <span>
                {{ $variant['stock'] > 0
                    ? __('website.pieces_available', ['count' => $variant['stock']])
                    : __('website.out_of_stock') }}
            </span>
        </button>
    @endforeach
@endif

                        <p class="product-details-message" role="status" aria-live="polite">
                            @if (session()->has('message'))
                                {{ session('message') }}
                            @endif
                        </p>

                        <div class="product-details-actions">
                            <button
                                type="button"
                                class="product-details-wishlist {{ $this->is_wishlisted ? 'is-active' : '' }}"
                                aria-pressed="{{ $this->is_wishlisted ? 'true' : 'false' }}"
                                wire:click="toggleWishlist"
                                wire:loading.attr="disabled"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" />
                                </svg>

                                <span>
                                    {{ $this->is_wishlisted
                                        ? __('website.remove_from_wishlist')
                                        : __('website.add_to_wishlist') }}
                                </span>
                            </button>
                        </div>

                    </div>
                </div>

                @if ($product->desc)
                    <h2 class="product-description-title">
                        {{ __('website.description') }}
                    </h2>
                    <p class="product-details-description">{{ $product->desc }}</p>
                @endif
            </div>
        </div>
    </section>
</div>
