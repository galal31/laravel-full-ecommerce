<!DOCTYPE html>
<html class="loading" lang="{{ app()->getLocale() }}" data-textdirection="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - {{ __('errors.403_title') }}</title>
    
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/vendors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/app.css') }}">
    
    <style>
        body {
            background-color: #f4f5fa;
            font-family: 'Cairo', sans-serif; /* أو الخط اللي بتستخدمه */
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: #FF4961; /* لون الـ Danger في ثيمك */
            text-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 0;
        }
        .error-icon {
            font-size: 6rem;
            vertical-align: middle;
        }
    </style>
</head>
<body class="vertical-layout vertical-menu 1-column blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-md-6 col-10 p-0">
                            <div class="card border-grey border-lighten-3 px-2 py-4 m-0 box-shadow-2 text-center rounded">
                                <div class="card-header border-0 pb-0">
                                    <h1 class="error-code">
                                        <i class="ft-lock error-icon"></i> 403
                                    </h1>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <h2 class="text-uppercase font-weight-bold">{{ __('errors.403_heading') }}</h2>
                                        <p class="text-muted mt-2 mb-4" style="font-size: 1.1rem;">
                                            {{ __('errors.403_message') }}
                                        </p>
                                        
                                        <a href="{{ route('dashboard.welcome') }}" class="btn btn-info btn-lg px-3 py-1">
                                            <i class="ft-home"></i> {{ __('errors.back_to_home') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

</body>
</html>