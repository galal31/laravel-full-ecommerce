@if ($order->payment_review_reason !== null)
    @php
        $reason = match ($order->payment_review_reason) {
            'stock_unavailable' => __('orders.reasons.stock_unavailable'),
            'coupon_unavailable' => __('orders.reasons.coupon_unavailable'),
            'payment_recorded_order_pending' => __('orders.reasons.payment_recorded_order_pending'),
            default => __('orders.reasons.other'),
        };
    @endphp
    <div class="order-review">
        <span class="badge badge-warning">{{ __('orders.needs_review') }}</span>
        <div class="small mt-1">{{ $reason }}</div>
    </div>
@else
    <span class="text-muted">—</span>
@endif
