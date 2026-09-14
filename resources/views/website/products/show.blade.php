@extends('layouts.website.userlayout')

@section('title', $product->name)

@section('content')
    @livewire('website.product-details', [
        'product' => $product,
    ])

    @include('website.partials.related-products', [
        'relatedProducts' => $relatedProducts,
    ])
@endsection
