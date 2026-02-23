@extends('layouts.dashboard.master_auth')
@section('title', __('auth.recover_title'))
@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="col-md-4 col-10 box-shadow-2 p-0">
                    <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                        <div class="card-header border-0 pb-0">
                            <div class="card-title text-center">
                                <img src="{{ asset('dashboard-assets/app-assets/images/logo/logo.png') }}"
                                    alt="branding logo">
                            </div>
                            <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                <span>{{ __('auth.send_link_msg') }}</span>
                            </h6>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success text-center mt-2">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                <form class="form-horizontal" action="{{ route('dashboard.postRecoverPassword') }}"
                                    method="POST">
                                    @csrf
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="email" class="form-control form-control-lg input-lg" name="email"
                                            placeholder="{{ __('auth.email_placeholder') }}" required>
                                        <div class="form-control-position">
                                            <i class="ft-mail"></i>
                                        </div>
                                        @error('email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </fieldset>
                                    <button type="submit" class="btn btn-outline-info btn-lg btn-block"><i
                                            class="ft-unlock"></i> {{ __('auth.recover_btn') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer border-0">
                            <p class="float-sm-left text-center"><a href="{{ route('dashboard.login') }}"
                                    class="card-link">{{ __('auth.login_link') }}</a></p>
                            <p class="float-sm-right text-center">{{ __('auth.new_account_msg') }} <a
                                    href="register-simple.html" class="card-link">{{ __('auth.create_account_link') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
