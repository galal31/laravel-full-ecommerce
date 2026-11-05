@php
    $color = match ($order->status) {
        'pending' => 'warning',
        'completed' => 'success',
        'cancelled' => 'danger',
        'delivered' => 'info',
    };
@endphp
<span class="badge badge-{{ $color }}">{{ __('orders.statuses.'.$order->status) }}</span>
