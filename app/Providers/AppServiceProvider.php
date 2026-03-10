<?php

namespace App\Providers;

use App\Models\Dashboard\Admin;
use App\Models\Dashboard\Role;
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
