<?php

namespace App\Providers;

use App\Models\Dashboard\Admin;
use App\Models\Dashboard\Role;
use App\Models\Dashboard\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('dashboard.*', function ($view) {

            $roles_count = Cache::rememberForever('dashboard_roles', function () {
                return Role::count();
            });
            $admins_count = Cache::rememberForever('dashboard_admins_count', function () {
                return Admin::count();
            });
            $view->with(['dashboard_roles_count' => $roles_count, 'dashboard_admins_count' => $admins_count]);
            Cache::forget('site_settings');
            $settings = Cache::rememberForever('site_settings', function () {
                $settings = Setting::firstOrCreate(
                    ['id' => 1], // شرط البحث
                    [
                        'site_name' => ['ar' => 'جلال وآية ستور', 'en' => 'Jalal & Aya Store'],
                        'address' => ['ar' => 'القاهرة، مصر', 'en' => 'Cairo, Egypt'],
                        'phone' => '01000000000',
                        'email' => 'info@domain.com',
                        'email_support' => 'support@domain.com',
                        'facebook' => 'https://facebook.com',
                        'twitter' => 'https://twitter.com',
                        'youtube' => 'https://youtube.com',
                        'logo' => 'logo.png',
                        'favicon' => 'favicon.png',
                    ]
                );
                return $settings;
            });

            $view->with(['settings' => $settings]);

        });


        // DEFINE THE GATES
        $config_permissions = array_keys(config('permissions'));
        foreach ($config_permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
