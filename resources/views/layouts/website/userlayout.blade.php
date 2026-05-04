<!doctype html>
<html lang="en">


<head>
<meta charset="utf-8">
<meta name="keywords" content="ShopUS, bootstrap-5, bootstrap, sass, css, HTML Template, HTML,html, bootstrap template, free template, figma, web design, web development,front end, bootstrap datepicker, bootstrap timepicker, javascript, ecommerce template">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{{ asset('website-assets/assets/images/homepage-one/icon.png') }}">

<title>@yield('title', 'Shopus')</title>

<link rel="stylesheet" href="{{ asset('website-assets/css/swiper10-bundle.min.css') }}">

<link rel="stylesheet" href="{{ asset('website-assets/css/bootstrap-5.3.2.min.css') }}">

<link rel="stylesheet" href="{{ asset('website-assets/css/nouislider.min.css') }}">

<link rel="stylesheet" href="{{ asset('website-assets/css/aos-3.0.0.css') }}">

<link rel="stylesheet" href="{{ asset('website-assets/css/style.css') }}">
@stack('styles')
</head>
<body>

@include('layouts.website.user.header')

<main>
    @yield('content')
</main>

@include('layouts.website.user.footer')

<script src="{{ asset('website-assets/assets/js/jquery_3.7.1.min.js') }}"></script>

<script src="{{ asset('website-assets/assets/js/bootstrap_5.3.2.bundle.min.js') }}"></script>

<script src="{{ asset('website-assets/assets/js/nouislider.min.js') }}"></script>

<script src="{{ asset('website-assets/assets/js/aos-3.0.0.js') }}"></script>

<script src="{{ asset('website-assets/assets/js/swiper10-bundle.min.js') }}"></script>

<script src="{{ asset('website-assets/assets/js/shopus.js') }}"></script>
@stack('scripts')
</body>
</html>
