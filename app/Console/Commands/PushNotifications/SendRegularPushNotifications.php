<?php

namespace App\Console\Commands\PushNotifications;

use App\Models\PushNotification;
use App\Services\Client\PushNotification\PushNotificationService;
use App\Services\Common\OneSignal\Exceptions\ApplicationHasNoSubscriberException;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRegularPushNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-regular-push-notifications';

    protected OneSignalNotificationService $oneSignalNotificationService;
    protected PushNotificationService $pushNotificationService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for ';

    public function __construct(OneSignalNotificationService $oneSignalNotificationService, PushNotificationService $pushNotificationService)
    {
        $this->oneSignalNotificationService = $oneSignalNotificationService;
        $this->pushNotificationService = $pushNotificationService;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $pushNotifications = $this->pushNotificationService->getActiveRegularPushesForCreate();

            /** @var PushNotification $pushNotification */
            foreach ($pushNotifications as $pushNotification) {
                // TODO log result ?
                try {
                    $this->oneSignalNotificationService->create($pushNotification);
                } catch (ApplicationHasNoSubscriberException $e) {
                    Log::warning($e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::error($e);
            $this->error($e->getMessage());
        }
    }
}
