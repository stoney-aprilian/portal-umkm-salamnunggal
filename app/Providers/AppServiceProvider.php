<?php

namespace App\Providers;

use App\View\Composers\PortalSettingsComposer;
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
        View::composer(
            ['layouts.app', 'layouts.guest', 'layouts.navigation', 'layouts.footer'],
            PortalSettingsComposer::class,
        );

        View::composer(
            'layouts.navigation',
            AdminVerificationCounterComposer::class,
        );
    }
}
