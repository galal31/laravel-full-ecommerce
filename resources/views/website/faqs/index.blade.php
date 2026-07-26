@extends('layouts.website.userlayout')

@section('title', __('faqs.page_title'))

@push('styles')
    <style>
        .faq-page-heading {
            background: #fffafe;
            padding: 5rem 0;
            text-align: center;
        }

        .faq-page-heading h1 {
            font-size: 3.6rem;
            margin-bottom: 1rem;
        }

        .faq-page-heading p {
            font-size: 1.6rem;
            margin: 0 auto;
            max-width: 68rem;
        }

        .faq-page-content {
            background: #fff;
            padding: 6rem 0;
        }

        .faq-list {
            margin: 0 auto;
            max-width: 92rem;
        }

        .faq-list .accordion-item {
            border: 1px solid #e8e8e8;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .faq-list .accordion-button {
            background: #fff;
            box-shadow: none;
            color: #232532;
            font-family: jost, sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            line-height: 1.5;
            padding: 2rem 2.2rem;
            text-align: start;
        }

        .faq-list .accordion-button:not(.collapsed) {
            background: rgba(174, 28, 154, 0.08);
            color: #ae1c9a;
        }

        .faq-list .accordion-button:focus {
            border-color: transparent;
            box-shadow: 0 0 0 2px rgba(174, 28, 154, 0.2);
        }

        .faq-list .accordion-body {
            color: #666;
            font-family: inter, sans-serif;
            font-size: 1.6rem;
            line-height: 1.9;
            padding: 2rem 2.2rem;
        }

        .faq-empty {
            color: #797979;
            font-size: 1.6rem;
            padding: 4rem 0;
            text-align: center;
        }

        @media (max-width: 767px) {
            .faq-page-heading {
                padding: 3rem 0;
            }

            .faq-page-heading h1 {
                font-size: 2.8rem;
            }

            .faq-page-content {
                padding: 3.5rem 0;
            }

            .faq-list .accordion-button,
            .faq-list .accordion-body {
                padding: 1.6rem;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="faq-page-heading">
            <div class="container">
                <h1>{{ __('faqs.page_title') }}</h1>
                <p>{{ __('faqs.page_intro') }}</p>
            </div>
        </section>

        <section class="faq-page-content">
            <div class="container">
                @if ($faqs->isNotEmpty())
                    <div class="accordion faq-list" id="faqAccordion">
                        @foreach ($faqs as $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                    <button
                                        class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faqCollapse{{ $faq->id }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="faqCollapse{{ $faq->id }}"
                                    >
                                        {{ $faq->getTranslation('question', app()->getLocale()) }}
                                    </button>
                                </h2>
                                <div
                                    id="faqCollapse{{ $faq->id }}"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="faqHeading{{ $faq->id }}"
                                    data-bs-parent="#faqAccordion"
                                >
                                    <div class="accordion-body">
                                        {!! nl2br(e($faq->getTranslation('answer', app()->getLocale()))) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="faq-empty">{{ __('faqs.no_public_faqs') }}</p>
                @endif
            </div>
        </section>

        @livewire('website.contact')
    </div>
@endsection
