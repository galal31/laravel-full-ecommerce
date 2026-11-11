<?php

namespace App\Providers;

use App\Models\Dashboard\Admin;
use App\Models\dashboard\Contact;
use App\Models\Dashboard\Role;
use App\Models\Dashboard\Setting;
use App\Models\Page;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        View::composer(['layouts.website.user.header', 'website.home-parts.footer'], function ($view) {
            $pages = Page::query()
                ->select(['title', 'slug'])
                ->orderBy('title')
                ->get();

            $view->with('dynamicPages', $pages);
        });

        View::composer('dashboard.*', function ($view) {

            $roles_count = Cache::rememberForever('dashboard_roles', function () {
                return Role::count();
            });
            $admins_count = Cache::rememberForever('dashboard_admins_count', function () {
                return Admin::count();
            });
            $view->with(['dashboard_roles_count' => $roles_count, 'dashboard_admins_count' => $admins_count]);
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
        View::composer('layouts.dashboard.partials.navbar', function ($view) {
            $currentAdmin = auth('admin')->user();
            // dd(Gate::allows('contact'));
            if (! $currentAdmin || ! $currentAdmin->hasPermission('notifications')) {
                $unreadCount = 0;
                $adminNotifications = collect();
            } else {
                $unreadCount = $currentAdmin ? $currentAdmin->unreadNotifications()->count() : 0;
                $adminNotifications = $currentAdmin ? $currentAdmin->notifications()->latest()->take(10)->get() : collect();
            }
            if (! $currentAdmin || ! $currentAdmin->hasPermission('contact')) {
                $unreadContactCount = 0;
                $adminContacts = collect();
            }else{
                $unreadContactCount = Contact::where('is_read', 0)->count();
                $adminContacts = Contact::latest()->take(10)->get();
            }

            $view->with([
                'unreadCount' => $unreadCount,
                'adminNotifications' => $adminNotifications,
                'unreadContactCount' => $unreadContactCount,
                'adminContacts' => $adminContacts,
            ]);
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
