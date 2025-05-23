<?php

namespace App\Providers;

use App\Services\Site\SiteRequestService;
use Illuminate\Support\ServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SiteRequestService::class);
    }
}
