<?php

namespace App\Listeners;

use App\Events\PwaClientClickCreated;
use App\Events\PwaClientEventCreated;
use App\Services\Common\ApplicationStatistic\UpdateStatisticService;

class UpdateApplicationStatistic
{
    /**
     * Handle the event.
     */
    public function handle(PwaClientClickCreated $event): void
    {
        /** @var UpdateStatisticService $updateStatisticService */
        $updateStatisticService = app(UpdateStatisticService::class, [
            'applicationId' => $event->pwaClientClick->getApplicationId(),
            'date' => $event->date,
        ]);

        $updateStatisticService->setClick();
        $updateStatisticService->save();
    }
}
