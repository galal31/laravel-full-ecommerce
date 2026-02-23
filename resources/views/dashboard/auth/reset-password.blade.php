@extends('layouts.dashboard.master_auth')
@section('title', __('auth.reset_title'))
@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="col-md-4 col-10 box-shadow-2 p-0">
                    <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                        <div class="card-header border-0 pb-0">
                            <div class="card-title text-center">
                                <img src="{{ asset('dashboard-assets/app-assets/images/logo/logo.png') }}" alt="branding logo">
                            </div>
                            <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                <span>{{ __('auth.reset_msg') }}</span>
                            </h6>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form-horizontal" action="{{ route('dashboard.postResetPassword') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="email" class="form-control form-control-lg input-lg" name="email" value="{{ $email ?? old('email') }}" readonly required>
                                        <div class="form-control-position"><i class="ft-mail"></i></div>
                                        @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </fieldset>

                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control form-control-lg input-lg" name="password" placeholder="{{ __('auth.password_placeholder') }}" required>
                                        <div class="form-control-position"><i class="ft-lock"></i></div>
                                        @error('password')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </fieldset>

                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control form-control-lg input-lg" name="password_confirmation" placeholder="{{ __('auth.password_confirm_placeholder') }}" required>
                                        <div class="form-control-position"><i class="ft-lock"></i></div>
                                    </fieldset>

                                    <button type="submit" class="btn btn-outline-info btn-lg btn-block"><i class="ft-unlock"></i> {{ __('auth.reset_btn') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection