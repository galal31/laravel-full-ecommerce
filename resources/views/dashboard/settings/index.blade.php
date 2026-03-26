@extends('layouts.dashboard.master')

@section('title', __('settings.site_settings'))

@section('content')
<div class="content-body">
    <section id="basic-form-layouts">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card box-shadow-2">
                    
                    <div class="card-header">
                        <h4 class="card-title">{{ __('settings.general_settings') }}</h4>
                    </div>

                    <div class="card-content collapse show">
                        <div class="card-body">

                            @if (session('success'))
                                <div class="alert alert-success text-center">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form setting-form" method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-body">
                                    <h4 class="form-section"><i class="ft-globe"></i> {{ __('settings.basic_info') }}</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.site_name_ar') }}</label>
                                                <input type="text" class="form-control" name="site_name[ar]" value="{{ old('site_name.ar', $settings->getTranslation('site_name', 'ar')) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.site_name_en') }}</label>
                                                <input type="text" class="form-control" name="site_name[en]" value="{{ old('site_name.en', $settings->getTranslation('site_name', 'en')) }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.address_ar') }}</label>
                                                <input type="text" class="form-control" name="address[ar]" value="{{ old('address.ar', $settings->getTranslation('address', 'ar')) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.address_en') }}</label>
                                                <input type="text" class="form-control" name="address[en]" value="{{ old('address.en', $settings->getTranslation('address', 'en')) }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="form-section mt-3"><i class="ft-mail"></i> {{ __('settings.contact_info') }}</h4>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.phone') }}</label>
                                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $settings->phone) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.general_email') }}</label>
                                                <input type="email" class="form-control" name="email" value="{{ old('email', $settings->email) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.support_email') }}</label>
                                                <input type="email" class="form-control" name="email_support" value="{{ old('email_support', $settings->email_support) }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="form-section mt-3"><i class="ft-share-2"></i> {{ __('settings.social_media') }}</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.facebook') }}</label>
                                                <input type="url" class="form-control" name="facebook" value="{{ old('facebook', $settings->facebook) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.twitter') }}</label>
                                                <input type="url" class="form-control" name="twitter" value="{{ old('twitter', $settings->twitter) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('settings.youtube') }}</label>
                                                <input type="url" class="form-control" name="youtube" value="{{ old('youtube', $settings->youtube) }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="form-section mt-3"><i class="ft-image"></i> {{ __('settings.images_logos') }}</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.main_logo') }}</label>
                                                <input type="file" class="form-control" name="logo" id="logo_input" disabled>
                                                @if($settings->logo)
                                                    <div class="mt-1">
                                                        <img src="{{ asset($settings->logo) }}" alt="Logo" height="50">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('settings.favicon') }}</label>
                                                <input type="file" class="form-control" name="favicon" id="favicon_input" disabled>
                                                @if($settings->favicon)
                                                    <div class="mt-1">
                                                        <img src="{{ asset($settings->favicon) }}" alt="Favicon" height="30">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions center mt-3">
                                    <button type="button" class="btn btn-info mr-1" id="edit_btn">
                                        <i class="ft-edit"></i> {{ __('settings.edit') }}
                                    </button>
                                    
                                    <button type="button" class="btn btn-warning mr-1" id="cancel_btn" hidden>
                                        <i class="ft-x"></i> {{ __('settings.cancel') }}
                                    </button>
                                    
                                    <button type="submit" class="btn btn-success" id="submit_btn" hidden>
                                        <i class="ft-check"></i> {{ __('settings.save_updates') }}
                                    </button>
                                </div>
                                
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#edit_btn').on('click', function() {
            $(this).attr('hidden', true);
            $('#submit_btn, #cancel_btn').removeAttr('hidden');
            
            $('.setting-form input[type="text"], .setting-form input[type="email"], .setting-form input[type="url"]').removeAttr('readonly');
            $('#logo_input, #favicon_input').removeAttr('disabled');
        });

        $('#cancel_btn').on('click', function() {
            $('#edit_btn').removeAttr('hidden');
            $('#submit_btn, #cancel_btn').attr('hidden', true);
            
            $('.setting-form input[type="text"], .setting-form input[type="email"], .setting-form input[type="url"]').attr('readonly', true);
            $('#logo_input, #favicon_input').attr('disabled', true);
            
            $('.setting-form')[0].reset();
        });
    });
</script>
@endsection