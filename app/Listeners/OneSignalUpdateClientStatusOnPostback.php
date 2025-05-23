<?php

namespace App\Listeners;

use App\Enums\PwaEvents\Event;
use App\Events\PwaClientEventCreated;
use App\Services\Common\OneSignal\OneSignalClientService;

class OneSignalUpdateClientStatusOnPostback
{
    public function handle(PwaClientEventCreated $event): void
    {
        if (
            $event->pwaClientEvent->event !== Event::Deposit->value
            && $event->pwaClientEvent->event !== Event::Registration->value
        ) {
            return;
        }

        try {
            /**
             * @var OneSignalClientService $service
             */
            $service = app(OneSignalClientService::class);
            $service->updateStatus($event->pwaClientEvent->pwaClientClick->pwaClient, $event->pwaClientEvent->event);
        } catch (\Throwable $throwable) {
            logger()->error('OneSignalUpdateClientStatusOnPostback@handle', [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTrace(),
                'event' => $event->pwaClientEvent->toArray(),
            ]);
        }
    }
}
