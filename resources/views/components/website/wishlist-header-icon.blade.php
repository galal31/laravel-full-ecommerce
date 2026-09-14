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
<div class="header-favourite">
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

    <a href="{{ route('website.wishlist.index') }}" class="cart-item">
        <span
            class="wishlist-icon-visual {{ $animationDirection }}"
            wire:key="wishlist-count-{{ $count }}"
        >
            <svg width="35" height="27" viewBox="0 0 35 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.4047 8.54989C11.6187 8.30247 11.8069 8.07783 12.0027 7.86001C15.0697 4.45162 20.3879 5.51717 22.1581 9.60443C23.4189 12.5161 22.8485 15.213 20.9965 17.6962C19.6524 19.498 17.95 20.9437 16.2722 22.4108C15.0307 23.4964 13.774 24.5642 12.5246 25.6408C11.6986 26.3523 11.1108 26.3607 10.2924 25.6397C8.05177 23.6657 5.79225 21.7125 3.59029 19.6964C2.35865 18.5686 1.33266 17.2553 0.638823 15.7086C-0.626904 12.8872 0.0324709 9.41204 2.22306 7.41034C4.84011 5.01855 8.81757 5.36918 11.1059 8.19281C11.1968 8.30475 11.2907 8.41404 11.4047 8.54989Z"
                    fill="#6E6D79"
                />
                <circle class="wishlist-badge-circle" cx="26.7662" cy="8" r="8" fill="#AE1C9A" />
                <text
                    class="wishlist-count-text"
                    x="26.7662"
                    y="8"
                    fill="#F9FFFB"
                    font-size="10"
                    font-weight="600"
                    text-anchor="middle"
                    dominant-baseline="central"
                >{{ $count }}</text>
            </svg>
        </span>

        <span class="cart-text">
            {{ __('site_header.wishlist') }}
        </span>
    </a>
</div>
