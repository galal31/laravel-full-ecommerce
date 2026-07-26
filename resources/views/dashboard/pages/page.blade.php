@extends('layouts.dashboard.master')

@section('title', __('pages.page_details') . ' | ' . $page->title)

@section('css')
    <style>
        .page-show {
            padding: 10px 0 30px;
        }

        .page-show-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .page-show-title {
            color: #2f3b52;
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 8px;
            word-break: break-word;
        }

        .page-show-meta {
            color: #7b8798;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0;
        }

        .page-show-card {
            background: #fff;
            border: 1px solid #e6ebf2;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            padding: 24px;
        }

        .page-content {
            color: #344054;
            font-size: 1rem;
            line-height: 1.9;
            overflow-wrap: anywhere;
        }

        .page-content :first-child {
            margin-top: 0;
        }

        .page-content :last-child {
            margin-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .page-show-header {
                flex-direction: column;
            }

            .page-show-header .btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-body">
        <section class="page-show">
            <div class="col-12">
                <div class="page-show-header">
                    <div>
                        <h4 class="page-show-title">{{ $page->title }}</h4>
                        <p class="page-show-meta">
                            <span>{{ __('pages.slug') }}: {{ $page->slug }}</span>
                            <span>{{ __('pages.created_at') }}: {{ $page->created_at?->format('Y-m-d h:i A') }}</span>
                        </p>
                    </div>

                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <a href="{{ route('dashboard.pages.index') }}" class="btn btn-outline-secondary">
                            <i class="ft-arrow-left"></i> {{ __('pages.back_to_pages') }}
                        </a>
                        <a href="{{ route('dashboard.pages.edit', $page->id) }}" class="btn btn-outline-primary">
                            <i class="ft-edit"></i> {{ __('datatables.edit') }}
                        </a>
                    </div>
                </div>

                <div class="page-show-card">
                    <div class="page-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
