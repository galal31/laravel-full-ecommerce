@extends('layouts.website.userlayout')

@section('title', __('auth.register.title'))
@section('content')
    <section class="login account footer-padding">
    <div class="container">
        <div class="login-section account-section">
            <div class="review-form">
                <h5 class="comment-title">{{ __('auth.register.title') }}</h5>
                
                <!-- بداية الـ Form -->
                <form action="{{ route('website.register') }}" method="POST">
                    @csrf 
                    
                    <div class="account-inner-form">
                        <div class="review-form-name">
                            <label for="fname" class="form-label">{{ __('auth.register.name_label') }}</label>
                            <input type="text" id="fname" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('auth.register.name_placeholder') }}" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="account-inner-form">
                        <div class="review-form-name">
                            <label for="email" class="form-label">{{ __('auth.register.email_label') }}</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('auth.register.email_placeholder') }}" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="review-form-name">
                            <label for="phone" class="form-label">{{ __('auth.register.phone_label') }}</label>
                            <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ __('auth.register.phone_placeholder') }}" value="{{ old('phone') }}" required>
                            @error('phone')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="account-inner-form">
                        <div class="review-form-name">
                            <label for="password" class="form-label">{{ __('auth.register.password_label') }}</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('auth.register.password_placeholder') }}" required>
                            @error('password')
                                <span class="text-danger" style="font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="review-form-name">
                            <label for="password_confirmation" class="form-label">{{ __('auth.register.password_confirm_label') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="{{ __('auth.register.password_placeholder') }}" required>
                        </div>
                    </div>
                    
                    @livewire('general.address-dropdown')

                    <div class="review-form-name checkbox">
                        <div class="checkbox-item">
                            <input type="checkbox" id="terms" name="terms_accepted" {{ old('terms_accepted') ? 'checked' : '' }} required>
                            <label for="terms" class="remember">
                                {{ __('auth.register.agree_terms') }} <span class="inner-text">{{ __('auth.register.shop_name') }}</span>
                            </label>
                        </div>
                        @error('terms_accepted')
                            <div class="text-danger mt-1" style="font-size: 0.875em;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="login-btn text-center">
                        <button type="submit" class="shop-btn" style="border: none; cursor: pointer; width: 100%;">{{ __('auth.register.button') }}</button>
                        <span class="shop-account">{{ __('auth.register.already_have_account') }} <a href="{{ route('website.login') }}">{{ __('auth.register.login_link') }}</a></span>
                    </div>
                </form>
                <!-- نهاية الـ Form -->
                
            </div>
        </div>
    </div>
</section>
@endsection