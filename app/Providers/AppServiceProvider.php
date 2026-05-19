<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        View::composer('components.layouts.web', function ($view) {
            if (auth()->check() && in_array(auth()->user()->role, ['admin', 'pengelola_tn'])) {
                $view->with('activeAnomalies', \App\Models\TrekkingLog::where('anomaly_flag', true)->count());
            }
        });
    }
}
