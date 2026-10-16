<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('home.store_description') }}">
    <title>@yield('title', __('home.page_title'))</title>
    <link rel="icon" href="{{ asset('website-assets/assets/images/homepage-one/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('website-assets/css/store-home.css') }}">
    <script src="{{ asset('website-assets/assets/js/store-home.js') }}" defer></script>
</head>
<body class="store-home" data-login-url="{{ route('website.login') }}"
      data-request-failed="{{ __('website.request_failed') }}"
      data-add-wishlist="{{ __('website.add_to_wishlist') }}"
      data-remove-wishlist="{{ __('website.remove_from_wishlist') }}">
    <a class="sh-skip" href="#main-content">{{ __('home.skip_content') }}</a>
    @include('website.home-parts.header')
    <main id="main-content" tabindex="-1">@yield('content')</main>
    @include('website.home-parts.footer')
    <div class="sh-toast" role="status" aria-live="polite" hidden></div>
</body>
</html>
