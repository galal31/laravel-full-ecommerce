@extends('layouts.website.userlayout')

@section('title', $page->title)

@push('styles')
    <style>
        .dynamic-page-heading {
            background: #fffafe;
            padding: 5rem 0;
            text-align: center;
        }

        .dynamic-page-heading h1 {
            font-size: 3.6rem;
            margin: 0;
            overflow-wrap: anywhere;
        }

        .dynamic-page-body {
            background: #fff;
            padding: 6rem 0;
        }

        .dynamic-page-content {
            color: #4f4f4f;
            font-family: inter, sans-serif;
            font-size: 1.6rem;
            line-height: 1.9;
            overflow-wrap: anywhere;
        }

        .dynamic-page-content h1,
        .dynamic-page-content h2,
        .dynamic-page-content h3,
        .dynamic-page-content h4,
        .dynamic-page-content h5,
        .dynamic-page-content h6 {
            margin: 2.5rem 0 1.2rem;
        }

        .dynamic-page-content p,
        .dynamic-page-content ul,
        .dynamic-page-content ol,
        .dynamic-page-content table,
        .dynamic-page-content blockquote {
            margin-bottom: 1.6rem;
        }

        .dynamic-page-content ul,
        .dynamic-page-content ol {
            padding-inline-start: 2.5rem;
        }

        .dynamic-page-content li {
            list-style: inherit;
        }

        .dynamic-page-content img {
            height: auto;
            max-width: 100%;
        }

        .dynamic-page-content table {
            display: block;
            max-width: 100%;
            overflow-x: auto;
        }

        .dynamic-page-content > :first-child {
            margin-top: 0;
        }

        .dynamic-page-content > :last-child {
            margin-bottom: 0;
        }

        @media (max-width: 767px) {
            .dynamic-page-heading {
                padding: 3rem 0;
            }

            .dynamic-page-heading h1 {
                font-size: 2.8rem;
            }

            .dynamic-page-body {
                padding: 3.5rem 0;
            }
        }
    </style>
@endpush

@section('content')
    <section class="dynamic-page-heading">
        <div class="container">
            <h1>{{ $page->title }}</h1>
        </div>
    </section>

    <section class="dynamic-page-body">
        <div class="container">
            <article class="dynamic-page-content">
                {!! $page->content !!}
            </article>
        </div>
    </section>
@endsection
