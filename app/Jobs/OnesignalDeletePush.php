<?php

namespace App\Jobs;

use App\Models\OneSignalNotification;
use App\Services\Common\OnesignalTemplate\OnesignalDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OnesignalDeletePush implements ShouldQueue
{
    use Queueable;

    /**
     * @param $onesignalTemplate
     */
    public function __construct(
        public OneSignalNotification $oneSignalNotification,
    ) {
        //
    }

    public function handle(OnesignalDeliveryService $deliveryService): void
    {
        $deliveryService->removeFromOnesignal($this->oneSignalNotification);
    }
}
