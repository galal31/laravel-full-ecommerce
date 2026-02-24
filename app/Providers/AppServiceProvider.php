<?php

namespace App\Providers;

use App\Models\Dashboard\Role;
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

        $roles = Role::count();

        $view->with('dashboard_roles', $roles);
    });


    // DEFINE THE GATES
    $config_permissions = array_keys(config('permissions'));
    foreach($config_permissions as $permission){
        Gate::define($permission, function ($user) use ($permission) {
            return $user->hasPermission($permission);
        });
    }
}
}
