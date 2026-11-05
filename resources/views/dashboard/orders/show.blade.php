@extends('layouts.dashboard.master')
@section('title', __('orders.details').' #'.$order->id)

@section('css')
    <style>
        .order-details { overflow-wrap: anywhere; }
        .order-details dt { color: #6b6f82; font-weight: 500; }
        .order-details dd { margin-bottom: .85rem; }
        .order-details th { white-space: nowrap; }
        .order-details td { vertical-align: middle; }
        .order-details .customer-note { white-space: pre-wrap; }
    </style>
@endsection

@section('content')
    @php
        $currency = config('services.myfatoorah.currency');
        $paymentRecorded = $order->transactions->contains(fn ($transaction) => $transaction->transaction_id !== null && $transaction->transaction_id !== '');
    @endphp
    <div class="content-body order-details" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
            <h3 class="mb-1">{{ __('orders.details') }} <bdi>#{{ $order->id }}</bdi></h3>
            <div class="d-flex flex-wrap">
                @if ($order->status === 'pending' && ! $paymentRecorded && $order->payment_expires_at->lessThanOrEqualTo(now()))
                    <form action="{{ route('dashboard.orders.cancel', $order->id) }}" method="POST" class="mx-1 mb-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">{{ __('orders.cancel_order') }}</button>
                    </form>
                @endif
                @if ($order->status === 'pending' && $order->payment_review_reason === 'stock_unavailable' && $paymentRecorded)
                    <form action="{{ route('dashboard.orders.recheck-stock', $order->id) }}" method="POST" class="mx-1 mb-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary">{{ __('orders.recheck_stock') }}</button>
                    </form>
                @endif
                @if ($order->status === 'completed')
                    <form action="{{ route('dashboard.orders.deliver', $order->id) }}" method="POST" class="mx-1 mb-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">{{ __('orders.mark_delivered') }}</button>
                    </form>
                @endif
                <a class="btn btn-outline-primary mb-1" href="{{ route('dashboard.orders.index') }}">{{ __('orders.back') }}</a>
            </div>
        </div>

        @include('layouts.dashboard.messages')

        @if ($order->payment_review_reason !== null)
            <div class="alert alert-warning" role="alert">
                @include('dashboard.orders._review', ['order' => $order])
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">{{ __('orders.order_info') }}</h4></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">{{ __('orders.number') }}</dt><dd class="col-sm-7"><bdi>#{{ $order->id }}</bdi></dd>
                            <dt class="col-sm-5">{{ __('orders.date') }}</dt><dd class="col-sm-7"><bdi>{{ $order->created_at->format('Y-m-d H:i:s') }}</bdi></dd>
                            <dt class="col-sm-5">{{ __('orders.status') }}</dt><dd class="col-sm-7">@include('dashboard.orders._status', ['order' => $order])</dd>
                            <dt class="col-sm-5">{{ __('orders.payment') }}</dt><dd class="col-sm-7">{{ $paymentRecorded ? __('orders.payment_recorded') : __('orders.payment_unconfirmed') }}</dd>
                            <dt class="col-sm-5">{{ __('orders.payment_deadline') }}</dt><dd class="col-sm-7"><bdi>{{ $order->payment_expires_at->format('Y-m-d H:i:s') }}</bdi></dd>
                        </dl>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h4 class="card-title">{{ __('orders.account') }}</h4></div>
                    <div class="card-body">
                        @if ($order->user)
                            <p><bdi>#{{ $order->user->id }}</bdi> — {{ $order->user->name }}</p>
                            <bdi>{{ $order->user->email }}</bdi>
                        @else
                            <p class="text-muted mb-0">{{ __('orders.account_unavailable') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">{{ __('orders.saved_customer') }}</h4></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ __('orders.customer') }}</dt><dd class="col-sm-8">{{ $order->user_name }}</dd>
                            <dt class="col-sm-4">{{ __('orders.phone') }}</dt><dd class="col-sm-8"><bdi>{{ $order->user_phone }}</bdi></dd>
                            <dt class="col-sm-4">{{ __('orders.email') }}</dt><dd class="col-sm-8"><bdi>{{ $order->user_email }}</bdi></dd>
                            <dt class="col-sm-4">{{ __('orders.country') }}</dt><dd class="col-sm-8">{{ $order->country }}</dd>
                            <dt class="col-sm-4">{{ __('orders.governorate') }}</dt><dd class="col-sm-8">{{ $order->governorate }}</dd>
                            <dt class="col-sm-4">{{ __('orders.city') }}</dt><dd class="col-sm-8">{{ $order->city }}</dd>
                            <dt class="col-sm-4">{{ __('orders.street') }}</dt><dd class="col-sm-8">{{ $order->street }}</dd>
                        </dl>
                        <h6>{{ __('orders.note') }}</h6>
                        <p class="customer-note mb-0">{{ $order->note ?: __('orders.no_note') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4 class="card-title">{{ __('orders.items') }}</h4></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr>
                        <th>{{ __('orders.product') }}</th><th>{{ __('orders.options') }}</th>
                        <th>{{ __('orders.quantity') }}</th><th>{{ __('orders.unit_price') }}</th><th>{{ __('orders.line_total') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            <tr>
                                <td>
                                    @if ($item->product_id)
                                        <a href="{{ route('dashboard.products.edit', $item->product_id) }}">{{ $item->product_name }}</a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </td>
                                <td>
                                    @forelse ($item->data ?: [] as $attribute => $value)
                                        <div>{{ $attribute }}: {{ $value }}</div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td>{{ $item->product_quantity }}</td>
                                <td><bdi>{{ number_format((float) $item->product_price, 2) }} {{ $currency }}</bdi></td>
                                <td><bdi>{{ number_format((float) $item->product_price * $item->product_quantity, 2) }} {{ $currency }}</bdi></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('orders.no_items') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4 class="card-title">{{ __('orders.summary') }}</h4></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ __('orders.subtotal') }}</dt><dd class="col-sm-7"><bdi>{{ number_format((float) $order->price, 2) }} {{ $currency }}</bdi></dd>
                    <dt class="col-sm-5">{{ __('orders.discount') }}</dt><dd class="col-sm-7"><bdi>{{ number_format((float) $order->coupon_discount_amount, 2) }} {{ $currency }}</bdi></dd>
                    <dt class="col-sm-5">{{ __('orders.coupon') }}</dt><dd class="col-sm-7"><bdi>{{ $order->coupon_code ?? '—' }}</bdi> @if ($order->coupon_code) <bdi>({{ $order->coupon_discount_percentage }}%)</bdi> @endif</dd>
                    <dt class="col-sm-5">{{ __('orders.coupon_usage') }}</dt><dd class="col-sm-7">{{ $order->coupon_usage_status ? __('orders.coupon_states.'.$order->coupon_usage_status) : __('orders.not_recorded') }}</dd>
                    <dt class="col-sm-5">{{ __('orders.shipping') }}</dt><dd class="col-sm-7"><bdi>{{ number_format((float) $order->shipping_price, 2) }} {{ $currency }}</bdi></dd>
                    <dt class="col-sm-5">{{ __('orders.total') }}</dt><dd class="col-sm-7 font-weight-bold"><bdi>{{ number_format((float) $order->total_price, 2) }} {{ $currency }}</bdi></dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4 class="card-title">{{ __('orders.transactions') }}</h4></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr>
                        <th>{{ __('orders.payment_method') }}</th><th>{{ __('orders.invoice') }}</th>
                        <th>{{ __('orders.transaction') }}</th><th>{{ __('orders.invoice_deadline') }}</th><th>{{ __('orders.record_date') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($order->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->payment_method === 'myfatoorah' ? __('orders.myfatoorah') : $transaction->payment_method }}</td>
                                <td><bdi>{{ $transaction->invoice_id ?? '—' }}</bdi></td>
                                <td><bdi>{{ $transaction->transaction_id ?? '—' }}</bdi></td>
                                <td><bdi>{{ $transaction->invoice_expires_at?->format('Y-m-d H:i:s') ?? '—' }}</bdi></td>
                                <td><bdi>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</bdi></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('orders.no_transactions') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
