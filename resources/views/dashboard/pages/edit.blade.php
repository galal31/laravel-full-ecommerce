@extends('layouts.dashboard.master')

@section('title', __('pages.edit_page'))

@section('css')
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jodit/4.12.37/es2021/jodit.min.css"
    >
@endsection

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">

                    <div class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">
                            {{ __('pages.edit_page') }}
                        </h4>

                        <a
                            href="{{ route('dashboard.pages.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="ft-arrow-left"></i>
                            {{ __('pages.back_to_pages') }}
                        </a>
                    </div>

                    <div class="card-content">
                        <div class="card-body">

                            <form
                                action="{{ route('dashboard.pages.update', $page->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="title">
                                        {{ __('pages.title') }}
                                    </label>

                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        value="{{ old('title', $page->title) }}"
                                        class="form-control @error('title') is-invalid @enderror"
                                        placeholder="{{ __('pages.title_placeholder') }}"
                                        required
                                    >

                                    @error('title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">
                                        {{ __('pages.slug') }}
                                    </label>

                                    <input
                                        type="text"
                                        id="slug"
                                        name="slug"
                                        value="{{ old('slug', $page->slug) }}"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        placeholder="{{ __('pages.slug_placeholder') }}"
                                        dir="ltr"
                                        required
                                    >

                                    <small class="form-text text-muted">
                                        {{ __('pages.slug_help') }}
                                    </small>

                                    @error('slug')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="content">
                                        {{ __('pages.content') }}
                                    </label>

                                    <textarea
                                        id="content"
                                        name="content"
                                        class="form-control @error('content') is-invalid @enderror"
                                        rows="12"
                                    >{{ old('content', $page->content) }}</textarea>

                                    @error('content')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <a
                                        href="{{ route('dashboard.pages.index') }}"
                                        class="btn btn-outline-secondary mr-1"
                                    >
                                        {{ __('messages.cancel') }}
                                    </a>

                                    <button
                                        type="submit"
                                        class="btn btn-info"
                                    >
                                        <i class="ft-save"></i>
                                        {{ __('pages.save_page') }}
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/4.12.37/es2021/jodit.min.js"></script>

    <script>
        Jodit.make('#content', {
            height: 400,
            direction: "{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}",
            language: "{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}",
            placeholder: "{{ __('pages.content_placeholder') }}"
        });
    </script>
@endsection
