<?php

use App\Models\CartItem;
use Livewire\Component;
use Livewire\Attributes\On;


new class extends Component
{
    public int $count = 0;
    public function mount()
    {
        $this->refreshCount();
    }

    #[On('cart_item_added')]
    public function updateCount(): void
    {
        $this->refreshCount();
    }

    #[On('cart_item_deleted')]
    public function updateCountOnDelete(): void
    {
        $this->refreshCount();
    }

    private function refreshCount(): void
    {
        if (! auth()->check()) {
            $this->count = 0;

            return;
        }

        $this->count = CartItem::query()
            ->whereHas('cart', fn ($cartQuery) => $cartQuery
                ->where('user_id', auth()->id()))
            ->count();
    }
};
?>

<a
    href="{{ Route::has('website.cart.index') ? route('website.cart.index') : '#' }}"
    class="cart-item cart-header-livewire-link"
    aria-label="{{ __('site_header.cart') }}"
>
    <style>
        .cart-header-livewire-link .cart-header-icon {
            display: inline-flex;
            transition: transform 180ms ease;
        }

        .cart-header-livewire-link:hover .cart-header-icon {
            transform: translateY(-2px);
        }

        .mobile-menu .cart-header-livewire-link .cart-text {
            display: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .cart-header-livewire-link .cart-header-icon {
                transition: none;
            }
        }
    </style>

    <span class="cart-header-icon">
        <svg width="35" height="28" viewBox="0 0 35 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M16.4444 21.897C14.8444 21.897 13.2441 21.8999 11.6441 21.8963C9.79233 21.892 8.65086 21.0273 8.12595 19.2489C7.04294 15.5794 5.95756 11.9107 4.87166 8.24203C4.6362 7.4468 4.37783 7.25412 3.55241 7.25175C2.7786 7.24964 2.00507 7.25754 1.23127 7.24911C0.512247 7.24148 0.0157813 6.79109 0.000242059 6.15064C-0.0160873 5.48281 0.475637 5.01689 1.23232 5.00873C2.11121 4.99952 2.99089 4.99214 3.86951 5.01268C5.36154 5.04769 6.52014 5.93215 6.96393 7.35415C7.14171 7.92378 7.34055 8.49026 7.46382 9.07201C7.54968 9.47713 7.77881 9.49661 8.10566 9.49582C11.8335 9.48897 15.5611 9.49134 19.2889 9.49134C21.0825 9.49134 22.8761 9.48108 24.6694 9.49503C26.0848 9.50608 27.0907 10.4906 27.0156 11.7778C27.0006 12.0363 26.925 12.2958 26.8473 12.5457C26.1317 14.8411 25.4124 17.1351 24.6879 19.4279C24.1851 21.0186 23.0223 21.8826 21.3504 21.8944C19.7151 21.906 18.0797 21.897 16.4444 21.897Z"
                fill="#6E6D79"
            />
            <path
                d="M12.4012 27.5161C11.167 27.5227 10.1488 26.524 10.1345 25.2928C10.1201 24.0419 11.1528 22.9982 12.3967 23.0066C13.6209 23.0151 14.6422 24.0404 14.6436 25.2623C14.6451 26.4855 13.6261 27.5095 12.4012 27.5161Z"
                fill="#6E6D79"
            />
            <path
                d="M22.509 25.2393C22.5193 26.4842 21.5393 27.4971 20.3064 27.5155C19.048 27.5342 18.0272 26.525 18.0277 25.2622C18.0279 24.0208 19.0214 23.0161 20.2572 23.0074C21.4877 22.9984 22.4988 24.0006 22.509 25.2393Z"
                fill="#6E6D79"
            />
            <circle cx="26.9523" cy="8" r="8" fill="#AE1C9A" />
            <text
                x="26.9523"
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
        {{ __('site_header.cart') }}
    </span>
</a>
