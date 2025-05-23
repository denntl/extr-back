<?php

namespace App\Listeners;

use App\Events\PwaClientClickCreated;
use App\Events\PwaClientEventCreated;
use App\Services\Common\ApplicationStatistic\UpdateStatisticService;

class UpdateApplicationStatisticAfterEventAdded
{
    /**
     * Handle the event.
     */
    public function handle(PwaClientEventCreated $event): void
    {
        /** @var UpdateStatisticService $updateStatisticService */
        $updateStatisticService = app(UpdateStatisticService::class, [
            'applicationId' => $event->pwaClientEvent->getApplicationId(),
            'date' => $event->date,
        ]);

        $updateStatisticService->setEvent($event->pwaClientEvent);

        $updateStatisticService->save();
    }
}
