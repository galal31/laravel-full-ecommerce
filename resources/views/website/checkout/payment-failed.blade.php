@extends('layouts.website.userlayout')

@section('title', __('checkout_success.payment_unconfirmed'))

@section('content')
    <section class="container py-5 text-center">
        <h1 class="mb-4">{{ __('checkout_success.payment_unconfirmed') }}</h1>
        <p class="mb-4">{{ __('checkout_success.payment_unconfirmed_hint') }}</p>
        <a class="btn btn-outline-secondary px-4 py-3" href="{{ route('website.home') }}">{{ __('checkout_success.continue') }}</a>
    </section>
@endsection
