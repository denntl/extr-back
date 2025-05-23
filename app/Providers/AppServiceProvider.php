<?php

namespace App\Providers;

use App\Services\Common\Auth\PermissionService;
use App\Services\Common\DataListing\DataListingFactory;
use App\Services\Common\DataListing\FilterModelFactory;
use App\Services\Common\DataListing\ListingServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * RegisterRequest any application services.
     */
    public function register(): void
    {
        $this->app->bind(PermissionService::class, function ($app, $params) {
            $user = $params['user'] ?? Auth::user();
            return new PermissionService($user);
        });

        $this->app->bind(ListingServiceInterface::class, function () {
            $request = request();
            $entity = DataListingFactory::mapEntityName($request->route('entity'));
            $model = FilterModelFactory::init($entity, $request);
            return DataListingFactory::init($entity, $model);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
