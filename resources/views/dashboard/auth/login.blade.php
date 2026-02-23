@extends('layouts.dashboard.master_auth')

@section('title', __('auth.login.title'))

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="col-md-4 col-10 box-shadow-2 p-0">
                    <div class="card border-grey border-lighten-3 m-0">

                        <div class="card-header border-0">
                            <div class="card-title text-center">
                                <img src="{{ asset('dashboard-assets/app-assets/images/logo/logo.png') }}"
                                    alt="branding logo">
                            </div>

                            <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                <span>{{ __('auth.login.subtitle') }}</span>
                            </h6>
                        </div>

                        <div class="card-content">
                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success text-center mt-2">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                {{-- Global Errors --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Login Failed Message --}}
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form class="form-horizontal" method="POST" action="{{ route('dashboard.postLogin') }}">

                                    @csrf

                                    {{-- Email --}}
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="email"
                                            class="form-control input-lg @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}"
                                            placeholder="{{ __('auth.login.email_placeholder') }}" required>

                                        <div class="form-control-position">
                                            <i class="ft-user"></i>
                                        </div>
                                    </fieldset>

                                    {{-- Password --}}
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password"
                                            class="form-control input-lg @error('password') is-invalid @enderror"
                                            name="password" placeholder="{{ __('auth.login.password_placeholder') }}"
                                            required>

                                        <div class="form-control-position">
                                            <i class="la la-key"></i>
                                        </div>
                                    </fieldset>

                                    {{-- Remember --}}
                                    <div class="form-group row">
                                        <div class="col-md-6 col-12 text-center text-md-left">
                                            <fieldset>
                                                <input type="checkbox" id="remember-me" name="remember" class="chk-remember"
                                                    {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember-me">
                                                    {{ __('auth.login.remember_me') }}
                                                </label>
                                            </fieldset>
                                        </div>

                                        <div class="col-md-6 col-12 text-center text-md-right">
                                            <a href="{{ route('dashboard.recoverPassword') }}" class="card-link">
                                                {{ __('auth.login.forgot_password') }}
                                            </a>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-block btn-lg">
                                        <i class="ft-unlock"></i>
                                        {{ __('auth.login.button') }}
                                    </button>

                                </form>

                            </div>
                        </div>

                        <div class="card-footer border-0">
                            <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1">
                                <span>{{ __('auth.login.new_to_modern') }}</span>
                            </p>

                            <a href="#" class="btn btn-info btn-block btn-lg mt-3">
                                <i class="ft-user"></i>
                                {{ __('auth.login.register_button') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
