<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        Route::aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
        // Register activity logging middleware alias so it can be used in routes
        Route::aliasMiddleware('log.activity', \App\Http\Middleware\LogActivity::class);
        Route::aliasMiddleware('permission', \App\Http\Middleware\CheckPermission::class);
    }
}
