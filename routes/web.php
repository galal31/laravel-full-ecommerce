<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\website\HomeController;
use App\Models\Page;
use App\Services\dashboard\FaqService;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'as' => 'website.',
        'middleware' => [
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index')->name('home');
        });

        Route::get('/pages/{slug}', function (string $slug) {
            $page = Page::query()->where('slug', $slug)->firstOrFail();

            return view('website.pages.show', compact('page'));
        })->name('pages.show');

        Route::get('/faqs', function (FaqService $faqService) {
            $faqs = $faqService->getAll();

            return view('website.faqs.index', compact('faqs'));
        })->name('faqs.index');

        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        | المستخدم اللي عامل login مينفعش يدخل login/register
        */
        Route::middleware('guest')->group(function () {

            Route::controller(RegisterController::class)->group(function () {
                Route::get('/register', 'showRegistrationForm')->name('register');
                Route::post('/register', 'register')->name('postRegister');
            });

            Route::controller(LoginController::class)->group(function () {
                Route::get('/login', 'showLoginForm')->name('login');
                Route::post('/login', 'login')->name('postLogin');
            });

        });


        /*
        |--------------------------------------------------------------------------
        | Auth Routes
        |--------------------------------------------------------------------------
        | المستخدم لازم يكون عامل login
        */
        Route::middleware('auth')->group(function () {



            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        });

    }
);
