@extends('layouts.website.userlayout')

@section('title', __('website.wishlist_title'))

@section('content')
    @livewire('website.wishlist-table')
@endsection
