@extends('layouts.website.userlayout')

@section('title', __('home.all_brands'))

@push('styles')
    <style>
        .catalog-page-heading {
            background: #fffafe;
            padding: 5rem 0;
            text-align: center;
        }

        .catalog-page-heading h1 {
            font-size: 3.6rem;
            margin: 0;
        }

        .catalog-page-content {
            padding: 6rem 0;
        }

        .catalog-brand-image {
            display: block;
            height: 7rem;
            max-width: 13rem;
            object-fit: contain;
            width: 100%;
        }

        .catalog-page-empty {
            color: #797979;
            font-size: 1.6rem;
            margin: 0;
            padding: 3rem 0;
            text-align: center;
        }

        @media (max-width: 767px) {
            .catalog-page-heading {
                padding: 3rem 0;
            }

            .catalog-page-heading h1 {
                font-size: 2.8rem;
            }

            .catalog-page-content {
                padding: 3.5rem 0;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="catalog-page-heading">
            <div class="container">
                <h1>{{ __('home.all_brands') }}</h1>
            </div>
        </section>

        <section class="catalog-page-content">
            <div class="container">
                @if ($brands->isNotEmpty())
                    <div class="brand-section">
                        @foreach ($brands as $brand)
                            <a
                                class="product-wrapper"
                                href="{{ route('website.brands.products', $brand->slug) }}"
                                title="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                            >
                                <div class="wrapper-img">
                                    <img
                                        class="catalog-brand-image"
                                        src="{{ $brand->logo }}"
                                        alt="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="catalog-page-empty">{{ __('home.no_brands') }}</p>
                @endif
            </div>
        </section>
    </div>
@endsection
