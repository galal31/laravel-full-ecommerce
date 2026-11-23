<?php

use App\Models\Dashboard\Wishlist;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->with([
                'product' => fn ($query) => $query
                    ->select([
                        'id', 'name', 'slug', 'status', 'category_id', 'brand_id',
                        'price', 'discount', 'start_discount', 'end_discount',
                        'manage_stock', 'quantity', 'available_in_stock',
                    ])
                    ->withImages()
                    ->with(['category:id,name', 'brand:id,name'])
                    ->withExists([
                        'variants as has_variants',
                        'variants as variants_is_in_stock' => fn ($variants) => $variants->where('stock', '>', 0),
                    ])
                    ->withMin('variants', 'price'),
            ])
            ->get();

        return [
            'wishlist' => $wishlist,
        ];
    }
};
?>

<section aria-labelledby="account-wishlist-heading">
    <h2 id="account-wishlist-heading" class="h3 mb-3">{{ __('website.wishlist_title') }}</h2>

    <ul class="d-grid gap-3">
        @forelse ($wishlist as $item)
            @php
                $product = $item->product;
                $image = $product->images->first();
            @endphp

            <li class="account-overview-card d-flex align-items-center gap-3" wire:key="wishlist-item-{{ $item->id }}">
                <img src="{{ $image?->image_url ?? asset('website-assets/assets/images/store-placeholder.svg') }}"
                    class="flex-shrink-0 rounded object-fit-contain" width="64" height="64" alt="" loading="lazy">

                <div class="text-break" style="min-width: 0;">
                    @if ($product->status)
                        <a href="{{ route('website.products.show', $product->slug) }}">
                            {{ $product->name }}
                        </a>
                    @else
                        <strong class="fs-4">{{ $product->name }}</strong>
                    @endif

                    @if ($product->category)
                        <span class="account-order-label">{{ $product->category->name }}</span>
                    @endif
                    <span class="account-order-label">{{ $product->brand?->name ?? __('website.no_brand') }}</span>

                    @if ($product->status)
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <strong class="fs-4">
                                @if ($product->has_variants)
                                    {{ __('website.starting_from') }}
                                    <bdi>{{ number_format((float) $product->variants_min_price, 2) }} {{ __('website.currency') }}</bdi>
                                @else
                                    <bdi>{{ number_format($product->getPriceAfterDiscount(), 2) }} {{ __('website.currency') }}</bdi>
                                @endif
                            </strong>
                            <span class="account-order-label">
                                {{ __($product->isInStock() ? 'website.in_stock' : 'website.out_of_stock') }}
                            </span>
                        </div>
                    @else
                        <span class="account-order-label">{{ __('website.product_not_available') }}</span>
                    @endif
                </div>
            </li>
        @empty
            <li class="account-overview-card">
                <p>{{ __('website.wishlist_empty') }}</p>
            </li>
        @endforelse
    </ul>
</section>
