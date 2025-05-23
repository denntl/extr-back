<?php

namespace App\Providers;

use App\Events\Client\Application\ApplicationCreated;
use App\Events\Client\Application\ApplicationDeactivated;
use App\Events\Client\DomainStatusWasChanged;
use App\Events\Client\DomainWasCreated;
use App\Events\Client\UserStatusChanged;
use App\Events\PwaClientClickCreated;
use App\Events\PwaClientEventCreated;
use App\Listeners\Client\Application\AfterApplicationCreated;
use App\Listeners\Client\Application\AfterApplicationDeactivated;
use App\Listeners\Client\Domain\AfterDomainCreated;
use App\Listeners\Client\Domain\AfterDomainStatusChanged;
use App\Listeners\Client\User\AfterUserDeactivated;
use App\Listeners\OneSignalUpdateClientStatusOnPostback;
use App\Listeners\SendPixelPostbackAfterEventAdded;
use App\Listeners\UpdateApplicationStatistic;
use App\Listeners\UpdateApplicationStatisticAfterEventAdded;
use App\Listeners\UpdateCompanyBalanceAfterFirstInstall;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'App\Events\Client\UserWasAuthenticated' => [
            'App\Listeners\Client\Telegraph\AfterLoginMessage',
        ],
        UserStatusChanged::class => [
          AfterUserDeactivated::class,
        ],
        PwaClientEventCreated::class => [
            UpdateApplicationStatisticAfterEventAdded::class,
            SendPixelPostbackAfterEventAdded::class,
            OneSignalUpdateClientStatusOnPostback::class,
            UpdateCompanyBalanceAfterFirstInstall::class
        ],
        PwaClientClickCreated::class => [
            UpdateApplicationStatistic::class,
        ],
        DomainWasCreated::class => [
            AfterDomainCreated::class,
        ],
        DomainStatusWasChanged::class => [
            AfterDomainStatusChanged::class,
        ],
        ApplicationCreated::class => [
            AfterApplicationCreated::class,
        ],
        ApplicationDeactivated::class => [
            AfterApplicationDeactivated::class,
        ]
    ];
}
