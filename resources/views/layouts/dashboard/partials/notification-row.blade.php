@php
    $data = $notification?->data ?? [];
    $isPaid = ! $notification || $notification->type === 'paid_order';
    $orderId = $data['order_id'] ?? null;
    $isUnread = $notification?->read_at === null;
@endphp

<a class="admin-notification-row" @if ($notification)
    id="admin-notification-{{ $notification->id }}"
    href="{{ route('dashboard.orders.show', ['id' => $orderId, 'notification_id' => $notification->id]) }}"
@endif>
    <div @class(['media', 'bg-blue-grey bg-lighten-5' => $isUnread])>
        <div class="media-left align-self-center">
            @if ($isPaid)
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 38px; height: 38px; background-color: #28d094; color: #fff; box-shadow: 0 2px 6px rgba(40, 208, 148, 0.4);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                        <path d="M15 15l2 2 4-4"></path>
                    </svg>
                </div>
            @else
                <i class="ft-bell icon-bg-circle bg-cyan"></i>
            @endif
        </div>
        <div class="media-body">
            <h6 @class(['media-heading mb-0 notification-message', 'text-bold-700' => $isUnread, 'text-bold-600' => ! $isUnread])>
                {{ $isPaid && $orderId ? __('orders.order_paid', ['id' => $orderId]) : ($data['message'] ?? '') }}
            </h6>
            @if (! $notification || isset($data['total_price']))
                <p class="notification-text font-small-3 text-muted mb-0">
                    {{ app()->getLocale() == 'ar' ? 'الإجمالي:' : 'Total:' }}
                    <strong class="notification-total">{{ $notification ? number_format((float) $data['total_price'], 2) : '' }}</strong>
                </p>
            @endif
            <small>
                <time class="media-meta text-muted" datetime="{{ $notification?->created_at->toIso8601String() }}">
                    {{ $notification?->created_at->diffForHumans() ?? (app()->getLocale() == 'ar' ? 'الآن' : 'Just now') }}
                </time>
            </small>
        </div>
    </div>
</a>
