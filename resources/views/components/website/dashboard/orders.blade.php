<?php

use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Locked]
    public $orderItems = null;

    public function with(): array
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->select([
                'id',
                'user_id',
                'coupon_discount_percentage',
                'price',
                'shipping_price',
                'total_price',
                'status',
                'created_at',
                'payment_review_reason',
            ])
            ->latest()
            ->orderByDesc('id')
            ->paginate(10, pageName: 'ordersPage');

        return [
            'orders' => $orders,
            'currency' => config('services.myfatoorah.currency', 'EGP'),
        ];
    }

    public function getOrderItems(int $id): void
    {
        $order = auth()->user()->orders()->findOrFail($id);

        $this->orderItems = $order->items()
            ->with(['product:id', 'product.images:id,product_id,file_name'])
            ->get();
    }

    protected function updatedOrdersPage(): void
    {
        $this->orderItems = null;
    }
};
?>

<section class="account-orders" aria-labelledby="account-orders-heading">
    <h2 id="account-orders-heading">{{ __('website.dashboard_tabs.orders') }}</h2>
    @php
        $openOrderId = $orderItems?->first()?->order_id;
    @endphp

    <ul class="account-order-list">
        @forelse ($orders as $order)
            @php
                // مدفوع — قيد المراجعة: status = pending وpayment_review_reason فيه السبب.
                $status = $order->status === 'pending' && $order->payment_review_reason !== null
                    ? 'in_review'
                    : $order->status;
                $isOpen =  $openOrderId === $order->id;
            @endphp

            <li wire:key="order-{{ $order->id }}">
                <button type="button" class="account-order-row" wire:click.prevent="getOrderItems({{ $order->id }})"
                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                    <span class="account-order-heading">
                        <span>
                            <strong>{{ __('checkout_success.order_number') }} <bdi>#{{ $order->id }}</bdi></strong>
                            <time class="account-order-label" datetime="{{ $order->created_at->toDateString() }}">
                                {{ $order->created_at->locale(app()->getLocale())->translatedFormat('j M Y') }}
                            </time>
                        </span>
                        <span class="account-overview-status" data-status="{{ $status }}">{{ __('checkout_success.' . $status) }}</span>
                    </span>

                    <span class="account-order-totals">
                        <span>
                            <span class="account-order-label">{{ __('checkout_success.subtotal') }}</span>
                            <bdi>{{ number_format((float) $order->price, 2) }} {{ $currency }}</bdi>
                        </span>
                        <span>
                            <span class="account-order-label">{{ __('checkout_success.discount') }}</span>
                            <bdi>{{ $order->coupon_discount_percentage ?? 0 }}%</bdi>
                        </span>
                        <span>
                            <span class="account-order-label">{{ __('checkout_success.shipping') }}</span>
                            <bdi>{{ number_format((float) $order->shipping_price, 2) }} {{ $currency }}</bdi>
                        </span>
                        <span>
                            <span class="account-order-label">{{ __('checkout_success.total') }}</span>
                            <strong><bdi>{{ number_format((float) $order->total_price, 2) }} {{ $currency }}</bdi></strong>
                        </span>
                    </span>

                    <span class="account-order-details">{{ __('website.view_details') }}</span>
                </button>

                @if ($isOpen)
                    <div class="account-overview-card mt-2">
                        <h3 class="h4 mb-2">{{ __('checkout_success.items') }}</h3>
                        <ul>
                            @foreach ($orderItems as $item)
                                @php($image = $item->product?->images->first())
                                <li class="d-flex align-items-start gap-3 border-top py-3">
                                    <img src="{{ $image?->image_url ?? asset('website-assets/assets/images/store-placeholder.svg') }}"
                                        class="flex-shrink-0 rounded object-fit-contain" width="56" height="56" alt="" loading="lazy">
                                    <div class="text-break" style="min-width: 0;">
                                        <strong>{{ $item->product_name }}</strong>
                                        @if (is_array($item->data))
                                            @foreach ($item->data as $attribute => $value)
                                                <span class="account-order-label">{{ $attribute }}: {{ $value }}</span>
                                            @endforeach
                                        @endif
                                        <span class="account-order-label">
                                            <bdi>{{ $item->product_quantity }} × {{ number_format((float) $item->product_price, 2) }} {{ $currency }}</bdi>
                                        </span>
                                        <span class="account-order-label">
                                            {{ __('orders.line_total') }}:
                                            <strong><bdi>{{ number_format((float) $item->product_price * $item->product_quantity, 2) }} {{ $currency }}</bdi></strong>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </li>
        @empty
            <li class="account-overview-card">
                <p>{{ __('website.dashboard_overview.no_orders') }}</p>
            </li>
        @endforelse
    </ul>

    @if ($orderItems?->isEmpty())
        <p>{{ __('website.order_items_empty') }}</p>
    @endif

    {{ $orders->links() }}
</section>
