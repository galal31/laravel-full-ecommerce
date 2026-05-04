<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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
        Route::get('/', function () {
                return view('website.home');
            })->name('home');

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