<?php

namespace App\Providers;

use App\Models\Tariff;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Application\CommentService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Invite\InviteService;
use App\Services\Client\Notification\NotificationService;
use App\Services\Client\OnesignalTemplate\OnesignalTemplateService;
use App\Services\Client\Statistic\StatisticService;
use App\Services\Client\Team\TeamService;
use App\Services\Client\TelegramBot\TelegramBotService;
use App\Services\Client\User\UserService;
use App\Services\Common\File\FileService;
use App\Services\Common\OneSignal\MockOneSignalApiClient;
use App\Services\Common\OneSignal\OneSignalApiClient;
use App\Services\Common\Tariff\Enums\Type;
use App\Services\Common\Tariff\Exceptions\InvalidTariffTypeException;
use App\Services\Common\Tariff\InstallTariff;
use App\Services\Common\Tariff\TariffService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $services = [
            CompanyService::class,
            UserService::class,
            ApplicationService::class,
            FileService::class,
            CommentService::class,
            InviteService::class,
            TeamService::class,
            TelegramBotService::class,
            NotificationService::class,
            StatisticService::class,
            OnesignalTemplateService::class,
        ];

        foreach ($services as $serviceClass) {
            $this->app->bind($serviceClass, function (Application $app, $params) use ($serviceClass) {
                /** @var User $user */
                $user = Auth::user();
                $companyIdFromUser = isset($user->company_id) ? $user->company_id : null;
                $companyId = $params['companyId'] ?? $companyIdFromUser;
                return new $serviceClass($companyId);
            });
        }

        $this->app->bind(OneSignalApiClient::class, function ($app, $params) {
            if (empty($params['application']) || !$params['application'] instanceof \App\Models\Application) {
                throw new \ErrorException('Valid Application model is required');
            }

            if (
                (
                    empty(config('services.onesignal.api_organization_key')) ||
                    empty(config('services.onesignal.api_organization_id'))
                ) ||
                (
                    empty($params['isCreateApplicationRequest']) &&
                    (
                        empty($params['application']->onesignal_auth_key) ||
                        empty($params['application']->onesignal_id)
                    )
                )
            ) {
                return new MockOneSignalApiClient($params['application']);
            }

            return new OneSignalApiClient($params['application']);
        });

        $this->app->bind(TariffService::class, function ($app, $params) {
            $config = [
                Type::Install->value => new InstallTariff(),
            ];
            if (!empty($params['tariff_id'])) {
                $typeId = Tariff::where('id', $params['tariff_id'])->value('type_id');
                if (empty($config[$typeId])) {
                    throw new InvalidTariffTypeException();
                }
            } elseif (!empty($params['type_id'])) {
                $typeId = $params['type_id'];
                if (empty($config[$typeId])) {
                    throw new InvalidTariffTypeException();
                }
            } else {
                throw new InvalidTariffTypeException();
            }

            return new TariffService($config[$typeId]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
