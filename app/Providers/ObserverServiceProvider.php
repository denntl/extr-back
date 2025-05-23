<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\PwaClientEvent;
use App\Observers\ApplicationObserver;
use App\Observers\PwaClientEventObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Application::observe(ApplicationObserver::class);
    }
}
