<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\AppSettingComposer;

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
        // Share app settings with all views
        View::composer([
            'auth.login',
            'auth.register',
            'auth.forgot',
            'auth.reset-password',
            'layouts.admin',
            'layouts.manajer',
            'layouts.staff'
        ], AppSettingComposer::class);
    }
}
