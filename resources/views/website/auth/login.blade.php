@extends('layouts.website.userlayout')

@section('title', __('auth.login.title'))
@section('content')
    <section class="login account footer-padding">
    <div class="container">
        <div class="login-section account-section">
            <div class="review-form">
                <h5 class="comment-title">{{ __('auth.login.title') }}</h5>
                
                <!-- بداية الـ Form -->
                <form action="{{ route('website.postLogin') }}" method="POST">
                    @csrf 
                    
                    <div class="account-inner-form">
                        <div class="review-form-name" style="width: 100%;">
                            <label for="email" class="form-label">{{ __('auth.login.email_placeholder') }}</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('auth.login.email_placeholder') }}" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="account-inner-form">
                        <div class="review-form-name" style="width: 100%;">
                            <label for="password" class="form-label">{{ __('auth.login.password_placeholder') }}</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('auth.login.password_placeholder') }}" required>
                            @error('password')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="review-form-name checkbox d-flex justify-content-between align-items-center">
                        <div class="checkbox-item">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="remember">
                                {{ __('auth.login.remember_me') }}
                            </label>
                        </div>
                        <div class="forgot-pass">
                            {{-- <a href="{{ route('password.request') }}" style="font-size: 0.875em; text-decoration: underline;">
                                {{ __('auth.login.forgot_password') }}
                            </a> --}}
                        </div>
                    </div>
                    
                    <div class="login-btn text-center mt-4">
                        <button type="submit" class="shop-btn" style="border: none; cursor: pointer; width: 100%;">{{ __('auth.login.button') }}</button>
                        <span class="shop-account">{{ __('auth.login.new_to_modern') }} <a href="{{ route('website.register') }}">{{ __('auth.login.register_button') }}</a></span>
                    </div>
                </form>
                <!-- نهاية الـ Form -->
                
            </div>
        </div>
    </div>
</section>
@endsection