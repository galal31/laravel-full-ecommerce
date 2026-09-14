@extends('layouts.website.userlayout')

@section('title', __('home.all_categories'))

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

        .catalog-page-image {
            display: block;
            height: 8rem;
            object-fit: contain;
            width: 8rem;
        }

        .catalog-page-name {
            color: #232532;
            line-height: 1.5;
            margin: 0;
            text-align: center;
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
                <h1>{{ __('home.all_categories') }}</h1>
            </div>
        </section>

        <section class="catalog-page-content">
            <div class="container">
                @if ($categories->isNotEmpty())
                    <div class="category-section">
                        @foreach ($categories as $category)
                            <a
                                class="product-wrapper"
                                href="{{ route('website.categories.products', $category->slug) }}"
                            >
                                <div class="wrapper-img">
                                    <img
                                        class="catalog-page-image"
                                        src="{{ $category->icon }}"
                                        alt="{{ $category->getTranslation('name', app()->getLocale()) }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                                <h2 class="wrapper-details catalog-page-name">
                                    {{ $category->getTranslation('name', app()->getLocale()) }}
                                </h2>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="catalog-page-empty">{{ __('home.no_categories') }}</p>
                @endif
            </div>
        </section>
    </div>
@endsection
