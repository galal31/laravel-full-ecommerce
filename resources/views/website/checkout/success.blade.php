@extends('layouts.website.userlayout')

@section('title', __('checkout_success.title'))

@push('styles')
<style>
    .order-receipt { --receipt-accent: #ae1c9a; --receipt-ink: #292333; --receipt-muted: #6e6677; padding: 5rem 0 7rem; background: #fffafe; color: var(--receipt-ink); }
    .order-receipt .receipt-wrap { max-width: 1040px; margin: auto; }
    .order-receipt .receipt-intro { text-align: center; margin-bottom: 3rem; }
    .order-receipt .receipt-mark { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: #eaf8ef; color: #238636; margin-bottom: 1.6rem; }
    .order-receipt .receipt-mark.is-pending { background: #fff1d9; color: #946000; }
    .order-receipt h1 { font-size: clamp(2.6rem, 4vw, 3.6rem); font-weight: 800; margin-bottom: 1rem; }
    .order-receipt .receipt-intro p { font-size: 1.5rem; color: var(--receipt-muted); max-width: 560px; margin: auto; line-height: 1.8; }
    .order-receipt .receipt-sheet { background: #fff; border: 1px solid #eee3ed; border-top: 4px solid var(--receipt-accent); border-radius: 1.2rem; overflow: hidden; box-shadow: 0 12px 32px #49213c08; }
    .order-receipt .receipt-meta { display: flex; justify-content: space-between; align-items: center; gap: 2rem; flex-wrap: wrap; padding: 2.4rem 3rem; background: #fdf7fc; border-bottom: 1px dashed #ddc8da; }
    .order-receipt .receipt-meta dt { color: var(--receipt-muted); font-size: 1.2rem; margin-bottom: .6rem; font-weight: 500; }
    .order-receipt .receipt-meta dd { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .order-receipt .receipt-number { font-family: monospace; font-size: 2.4rem !important; color: var(--receipt-accent); }
    .order-receipt .receipt-status { display: inline-block; background: #eaf8ef; color: #238636; padding: .6rem 1.2rem; border-radius: 2rem; font-size: 1.2rem; }
    .order-receipt .receipt-status.is-pending { background: #fff1d9; color: #946000; }
    .order-receipt .receipt-body { display: grid; grid-template-columns: minmax(0, 1fr) 310px; }
    .order-receipt .receipt-products { padding: 3rem; }
    .order-receipt h2 { font-size: 1.8rem; font-weight: 700; margin-bottom: 2rem; }
    .order-receipt .receipt-item { display: flex; align-items: flex-start; justify-content: space-between; gap: 2rem; padding: 1.8rem 0; border-bottom: 1px solid #f1ebf0; }
    .order-receipt .receipt-item:first-of-type { padding-top: 0; }
    .order-receipt .receipt-item h3 { font-size: 1.5rem; line-height: 1.7; font-weight: 700; margin: 0 0 .4rem; overflow-wrap: anywhere; }
    .order-receipt .receipt-item p { font-size: 1.2rem; color: var(--receipt-muted); margin: .4rem 0; line-height: 1.8; }
    .order-receipt .receipt-item-total { font-size: 1.4rem; font-weight: 700; text-align: end; flex-shrink: 0; }
    .order-receipt .receipt-side { padding: 3rem 2.4rem; border-inline-start: 1px solid #eee3ed; background: #fefcfe; }
    .order-receipt .receipt-totals { margin-bottom: 3rem; }
    .order-receipt .receipt-totals div { display: flex; justify-content: space-between; gap: 1rem; font-size: 1.3rem; margin-bottom: 1.4rem; }
    .order-receipt .receipt-totals dt { font-weight: 500; color: var(--receipt-muted); }
    .order-receipt .receipt-totals dd { margin: 0; font-weight: 700; }
    .order-receipt .receipt-totals .receipt-total { border-top: 1px solid #e8dce6; padding-top: 1.6rem; font-size: 1.8rem; }
    .order-receipt .receipt-total dd { color: var(--receipt-accent); }
    .order-receipt address { font-style: normal; font-size: 1.3rem; color: var(--receipt-muted); line-height: 1.9; overflow-wrap: anywhere; }
    .order-receipt address strong { display: block; color: var(--receipt-ink); }
    .order-receipt .receipt-footer { padding: 1.8rem 3rem; border-top: 1px dashed #ddc8da; color: var(--receipt-muted); font-size: 1.2rem; text-align: center; margin: 0; }
    .order-receipt .receipt-actions { display: flex; gap: 1.2rem; justify-content: center; margin-top: 2.8rem; flex-wrap: wrap; }
    .order-receipt .receipt-button { display: inline-flex; align-items: center; justify-content: center; min-height: 46px; padding: 1rem 2.5rem; font-size: 1.4rem; font-weight: 700; border: 1px solid var(--receipt-accent); border-radius: .7rem; color: #fff; background: var(--receipt-accent); text-decoration: none; }
    .order-receipt .receipt-button.is-secondary { background: #fff; color: var(--receipt-accent); }
    .order-receipt .receipt-button:focus-visible { outline: 3px solid #57224f; outline-offset: 4px; }
    @media (max-width: 767px) { .order-receipt { padding: 3rem 0 4rem; } .order-receipt .receipt-body { grid-template-columns: 1fr; } .order-receipt .receipt-side { border-inline-start: 0; border-top: 1px solid #eee3ed; } .order-receipt .receipt-meta, .order-receipt .receipt-products, .order-receipt .receipt-side { padding: 2rem; } .order-receipt .receipt-meta { gap: 1.6rem; } .order-receipt .receipt-actions { flex-direction: column; } }
    @media print { body > header, body > footer, main ~ *, .order-receipt .receipt-actions { display: none !important; } .order-receipt { padding: 0; background: #fff; } .order-receipt .receipt-sheet { box-shadow: none; } .order-receipt .receipt-body { grid-template-columns: minmax(0, 1fr) 280px; } }
</style>
@endpush

@section('content')
@php
    $isCompleted = in_array($order->status, ['completed', 'delivered'], true);
    $needsReview = $order->payment_review_reason !== null;
    $statusLabel = $needsReview ? 'in_review' : (in_array($order->status, ['completed', 'pending', 'cancelled', 'delivered'], true)
        ? $order->status : 'other_status');
    $titleKey = $needsReview ? 'payment_received_review' : ($isCompleted ? 'confirmed' : 'received');
    $hintKey = $needsReview ? 'payment_received_review_hint' : ($isCompleted ? 'confirmed_hint' : 'received_hint');
    $currency = config('services.myfatoorah.currency', 'EGP');
@endphp
<section class="order-receipt" aria-labelledby="receipt-title">
    <div class="container">
        <div class="receipt-wrap">
            <div class="receipt-intro">
                <span class="receipt-mark {{ $isCompleted ? '' : 'is-pending' }}" aria-hidden="true">
                    @if ($isCompleted)
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m5 12 4 4L19 6"/></svg>
                    @else
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    @endif
                </span>
                <h1 id="receipt-title">{{ __('checkout_success.'.$titleKey) }}</h1>
                <p>{{ __('checkout_success.'.$hintKey) }}</p>
            </div>
            <article class="receipt-sheet">
                <dl class="receipt-meta mb-0">
                    <div><dt>{{ __('checkout_success.order_number') }}</dt><dd class="receipt-number"><bdi>#{{ $order->id }}</bdi></dd></div>
                    <div><dt>{{ __('checkout_success.date') }}</dt><dd><bdi>{{ $order->created_at->format('d / m / Y') }}</bdi></dd></div>
                    <div><dt>{{ __('checkout_success.status') }}</dt><dd><span class="receipt-status {{ $isCompleted ? '' : 'is-pending' }}">{{ __('checkout_success.'.$statusLabel) }}</span></dd></div>
                </dl>
                <div class="receipt-body">
                    <div class="receipt-products">
                        <h2>{{ __('checkout_success.items') }}</h2>
                        @foreach ($order->items as $item)
                            <div class="receipt-item">
                                <div>
                                    <h3>{{ $item->product_name }}</h3>
                                    @if (is_array($item->data))
                                        <p>@foreach ($item->data as $attribute => $value){{ $attribute }}: {{ $value }}{{ $loop->last ? '' : ' · ' }}@endforeach</p>
                                    @endif
                                    <p>{{ __('checkout_success.quantity') }}: {{ $item->product_quantity }} <span aria-hidden="true">×</span> <bdi>{{ number_format((float) $item->product_price, 2) }} {{ $currency }}</bdi></p>
                                </div>
                                <strong class="receipt-item-total"><bdi>{{ number_format((float) $item->product_price * $item->product_quantity, 2) }}<br>{{ $currency }}</bdi></strong>
                            </div>
                        @endforeach
                    </div>
                    <aside class="receipt-side">
                        <h2>{{ __('checkout_success.summary') }}</h2>
                        <dl class="receipt-totals">
                            <div><dt>{{ __('checkout_success.subtotal') }}</dt><dd><bdi>{{ number_format((float) $order->price, 2) }} {{ $currency }}</bdi></dd></div>
                            @if ((float) $order->coupon_discount_amount > 0)
                                <div><dt>{{ __('checkout_success.discount') }}</dt><dd><bdi>−{{ number_format((float) $order->coupon_discount_amount, 2) }} {{ $currency }}</bdi></dd></div>
                            @endif
                            <div><dt>{{ __('checkout_success.shipping') }}</dt><dd><bdi>{{ number_format((float) $order->shipping_price, 2) }} {{ $currency }}</bdi></dd></div>
                            <div class="receipt-total"><dt>{{ __('checkout_success.total') }}</dt><dd><bdi>{{ number_format((float) $order->total_price, 2) }} {{ $currency }}</bdi></dd></div>
                        </dl>
                        <h2>{{ __('checkout_success.address') }}</h2>
                        <address><strong>{{ $order->user_name }}</strong>{{ $order->street }}<br>{{ $order->city }}، {{ $order->governorate }}<br>{{ $order->country }}</address>
                    </aside>
                </div>
                <p class="receipt-footer">{{ __('checkout_success.keep_number') }}</p>
            </article>
            <div class="receipt-actions">
                <a class="receipt-button" href="{{ route('website.home') }}">{{ __('checkout_success.continue') }}</a>
                <button class="receipt-button is-secondary" type="button" onclick="window.print()">{{ __('checkout_success.print') }}</button>
            </div>
        </div>
    </div>
</section>
@endsection
