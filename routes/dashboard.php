<?php

use App\Http\Controllers\dashboard\AdminsController;
use App\Http\Controllers\dashboard\auth\AuthController;
use App\Http\Controllers\dashboard\RolesController;
use App\Http\Controllers\dashboard\WelcomeController;
use App\Http\Controllers\dashboard\WorldController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\LaravelLocalization as McamaraLaravelLocalization;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/dashboard',
        'as' => 'dashboard.',
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::group([], function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest:admin');
            Route::post('/login', [AuthController::class, 'login'])->name('postLogin')->middleware('guest:admin');
            Route::get('/recover-password', [AuthController::class, 'showRecoverPassword'])->name('recoverPassword');
            Route::post('/recover-password', [AuthController::class, 'recoverPassword'])->name('postRecoverPassword');
            Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('resetPassword');
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('postResetPassword');
        });

        ######################## authentication routes ########################

        Route::group(['middleware' => ['auth:admin']], function () {
            Route::get('/welcome',[WelcomeController::class,'index'])->name('welcome');
            Route::prefix('roles')->middleware('can:roles')->group(function () {

                Route::get('/',[RolesController::class,'index'])->name('roles.index');
                Route::post('/store',[RolesController::class,'store'])->name('roles.store');
                Route::put('/{id}/update', [RolesController::class, 'update'])->name('roles.update');
                Route::delete('/{id}/destroy', [RolesController::class, 'destroy'])->name('roles.destroy');
            });

            // admin routes
            Route::PATCH('admins/{id}/toggleStatus', [AdminsController::class, 'toggleStatus'])->name('admins.toggleStatus');
            Route::resource('admins',AdminsController::class)->middleware('can:admins');


            // world routes

            Route::get('/countries',[WorldController::class,'getAllCountries'])->name('countries.index');
            Route::PATCH('countries/{id}/toggleStatus', [WorldController::class, 'toggleStatus'])->name('countries.toggleStatus');
            // governorates routes
            Route::get('/governorates/{country_id?}',[WorldController::class,'gettAllGovernorates'])->name('governorates.index');
            Route::PATCH('governorates/{id}/toggleStatus', [WorldController::class, 'toggleStatus'])->name('governorates.toggleStatus');
            Route::PATCH('governorates/{id}/changePrice', [WorldController::class, 'GovernorateChangePrice'])->name('governorates.changePrice');
            // cities routes
            
            Route::get('/cities/{governorate_id?}',[WorldController::class,'getAllCities'])->name('cities.index');
            Route::PATCH('cities/{id}/toggleStatus', [WorldController::class, 'toggleStatus'])->name('cities.toggleStatus');
        });
    }
);