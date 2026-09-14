@extends('layouts.website.userlayout')

@section('title', __('website.cart_title'))

@section('content')
    @livewire('website.cart-table')
@endsection
