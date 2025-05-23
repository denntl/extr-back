<?php

namespace App\Services\Common\OneSignal;

use App\Models\OneSignalEvents;
use App\Services\Common\OneSignal\DTO\Webhook\WebhookDTO;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class OneSignalEventsService
{
    /**
     * @param WebhookDTO $webhookDTO
     * @return void
     * @throws Throwable
     */
    public function create(WebhookDTO $webhookDTO): void
    {
        try {
            DB::beginTransaction();

            $oneSignalEvent = OneSignalEvents::query()->create($webhookDTO->toArray());

            /** @var OneSignalNotificationService $oneSignalNotificationService */
            $oneSignalNotificationService = app(OneSignalNotificationService::class);

            $oneSignalNotificationService->updateLastWebhookAcceptedAt($oneSignalEvent->notification_id);
            $oneSignalNotificationService->incrementEventCounter($oneSignalEvent);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
