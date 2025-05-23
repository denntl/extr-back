<?php

namespace App\Console\Commands\PushNotifications;

use App\Enums\PushNotification\Status;
use App\Models\OneSignalNotification;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateNewPushNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-new-push-notifications';

    protected OneSignalNotificationService $oneSignalNotificationService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for updating new push notifications';

    public function __construct(OneSignalNotificationService $oneSignalNotificationService)
    {
        $this->oneSignalNotificationService = $oneSignalNotificationService;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $oneSignalNotifications = $this->oneSignalNotificationService->getPushNotificationsForUpdate();

            foreach ($oneSignalNotifications as $oneSignalNotification) {

                /** @var OneSignalNotification $oneSignalNotification */
                if ($oneSignalNotification->pushNotification === null) {
                    // TODO remove onesignal notification when removed push notification ?
                    continue;
                }

                try {
                    $oneSignalNotificationData = $this->oneSignalNotificationService->get($oneSignalNotification);
                } catch (\Throwable $e) {
                    Log::error($e);
                    $this->error($e->getMessage());
                    continue;
                }

                $updateData = [
                    'sent' => $oneSignalNotificationData['successful'], // check it
                    'queued_at' => Carbon::parse($oneSignalNotificationData['queued_at']),
                    'completed_at' => Carbon::parse($oneSignalNotificationData['completed_at']),
                ];
                $oneSignalNotification->fill($updateData);

                if (!empty($oneSignalNotificationData['completed_at'])) {
                    $oneSignalNotification->pushNotification->update([
                        'status' => Status::Sent->value,
                    ]);
                }

                if ($oneSignalNotification->isDirty()) {
                    $oneSignalNotification->update($updateData);
                }
            }
        } catch (\Throwable $e) {
            Log::error($e);
            $this->error($e->getMessage());
        }
    }
}
