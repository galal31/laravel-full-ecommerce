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

<a class="sh-header-action sh-cart-link" href="{{ route('website.cart.index') }}">
    <span class="sh-header-icon">
        @include('website.home-parts.icon', ['name' => 'cart'])
        <span class="sh-header-badge">{{ $count }}</span>
    </span>
    <span>{{ __('home.cart') }}</span>
</a>
