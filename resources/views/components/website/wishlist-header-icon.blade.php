<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public int $count = 0;
    public string $animationDirection = '';

    public function mount()
    {
        if (!auth()->check()) {
            return;
        }
        if (auth()->check()) {
            $this->count = auth()->user()->wishlists()->count();
        }
    }

    #[On('wishlist_item_added')]
    public function incrementWishlistCount()
    {
        $this->count++;
        $this->animationDirection = 'count-increase';
    }

    #[On('wishlist_item_removed')]
    public function decrementWishlistCount()
    {
        if ($this->count > 0) {
            $this->count--;
            $this->animationDirection = 'count-decrease';
        }
    }
};
?>
<a class="sh-header-action sh-wishlist-link" href="{{ route('website.wishlist.index') }}">
    <style>
        .wishlist-icon-visual {
            display: inline-flex;
        }

        .wishlist-icon-visual .wishlist-badge-circle {
            transform-box: fill-box;
            transform-origin: center;
        }

        .wishlist-icon-visual .wishlist-count-text {
            transform-box: fill-box;
            transform-origin: center;
        }

        .wishlist-icon-visual.count-increase .wishlist-badge-circle,
        .wishlist-icon-visual.count-decrease .wishlist-badge-circle {
            animation: wishlist-badge-pulse 350ms ease-out;
        }

        .wishlist-icon-visual.count-increase .wishlist-count-text {
            animation: wishlist-count-increase 350ms ease-out;
        }

        .wishlist-icon-visual.count-decrease .wishlist-count-text {
            animation: wishlist-count-decrease 300ms ease-out;
        }

        @keyframes wishlist-badge-pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.25);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes wishlist-count-increase {
            0% {
                opacity: 0;
                transform: translateY(6px) scale(0.85);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes wishlist-count-decrease {
            0% {
                opacity: 0;
                transform: translateY(-6px) scale(0.85);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .wishlist-icon-visual .wishlist-badge-circle,
            .wishlist-icon-visual .wishlist-count-text {
                animation: none !important;
            }
        }
    </style>

    <span class="sh-header-icon wishlist-icon-visual {{ $animationDirection }}" wire:key="wishlist-count-{{ $count }}">
        @include('website.home-parts.icon', ['name' => 'heart'])
        <span class="sh-header-badge wishlist-badge-circle"><span class="wishlist-count-text">{{ $count }}</span></span>
    </span>
    <span>{{ __('home.wishlist') }}</span>
</a>
