<?php

use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $user = auth()->user();
        $user->loadCount([
            'orders',
            'orders as delivered_orders_count' => fn ($query) => $query->where('status', 'delivered'),
            'wishlists',
        ]);


        $recentOrders = $user->orders()->latest()->limit(3)->get();

        return [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'latestOrder' => $recentOrders->first(),
            'currency' => config('services.myfatoorah.currency', 'EGP'),
        ];
    }
};

?>
<div class="account-overview">
    <section class="account-overview-welcome" aria-labelledby="account-overview-heading">
        <div class="account-overview-user">
            <h2 id="account-overview-heading">{{ __('website.dashboard_overview.welcome', ['name' => $user->name]) }}</h2>
            <p><bdi>{{ $user->email }}</bdi></p>
            @if ($user->phone)
                <p><bdi>{{ $user->phone }}</bdi></p>
            @endif
        </div>
        <button type="button" class="account-overview-action" wire:click.prevent="$parent.selectTab('profile')">
            {{ __('website.dashboard_overview.edit_profile') }}
        </button>
    </section>

    <dl class="account-overview-stats">
        <div class="account-overview-card">
            <dt>{{ __('website.dashboard_overview.total_orders') }}</dt>
            <dd>{{ $user->orders_count }}</dd>
        </div>
        <div class="account-overview-card">
            <dt>{{ __('website.dashboard_overview.delivered_orders') }}</dt>
            <dd>{{ $user->delivered_orders_count }}</dd>
        </div>
        <div class="account-overview-card">
            <dt>{{ __('website.dashboard_overview.saved_products') }}</dt>
            <dd>{{ $user->wishlists_count }}</dd>
        </div>
    </dl>

    <div class="row g-3">
        <section class="col-12 col-xl-8" aria-labelledby="account-overview-orders">
            <div class="account-overview-card">
                <div class="account-overview-card-heading">
                    <h3 id="account-overview-orders">{{ __('website.dashboard_overview.recent_orders') }}</h3>
                    <button type="button" class="account-overview-action" wire:click.prevent="$parent.selectTab('orders')">
                        {{ __('website.dashboard_overview.view_orders') }}
                    </button>
                </div>
                <ul class="account-overview-orders">
                    @forelse ($recentOrders as $order)
                        @php($status = $order->payment_review_reason !== null ? 'in_review' : $order->status)
                        <li class="account-overview-order">
                            <div>
                                <strong>{{ __('checkout_success.order_number') }} <bdi>#{{ $order->id }}</bdi></strong>
                                <time datetime="{{ $order->created_at->toDateString() }}">
                                    {{ $order->created_at->locale(app()->getLocale())->translatedFormat('j M Y') }}
                                </time>
                            </div>
                            <div class="account-overview-order-summary">
                                <span class="account-overview-status" data-status="{{ $status }}">
                                    {{ __('checkout_success.' . $status) }}
                                </span>
                                <strong><bdi>{{ number_format((float) $order->total_price, 2) }} {{ $currency }}</bdi></strong>
                            </div>
                        </li>
                    @empty
                        <li><p>{{ __('website.dashboard_overview.no_orders') }}</p></li>
                    @endforelse
                </ul>
            </div>
        </section>

        <section class="col-12 col-xl-4" aria-labelledby="account-overview-address">
            <div class="account-overview-card">
                <h3 id="account-overview-address">{{ __('website.dashboard_overview.last_shipping_address') }}</h3>
                @if ($latestOrder)
                    <address>
                        {{ $latestOrder->street }}<br>
                        {{ $latestOrder->city }}<br>
                        {{ $latestOrder->governorate }}<br>
                        {{ $latestOrder->country }}
                    </address>
                @else
                    <p>{{ __('website.dashboard_overview.no_shipping_address') }}</p>
                @endif
            </div>
        </section>
    </div>
</div>
