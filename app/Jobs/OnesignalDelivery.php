<?php

namespace App\Jobs;

use App\Services\Client\OnesignalTemplate\DTOs\TemplateToSendDTO;
use App\Services\Common\OnesignalTemplate\OnesignalDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OnesignalDelivery implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TemplateToSendDTO $onesignalTemplate //one signal template with application id
    ) {
        //
    }

    public function handle(OnesignalDeliveryService $deliveryService): void
    {
        $deliveryService->sendToOnesignal($this->onesignalTemplate);
    }
}
