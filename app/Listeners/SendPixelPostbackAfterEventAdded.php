<?php

namespace App\Listeners;

use App\Enums\PwaEvents\Event;
use App\Events\PwaClientEventCreated;
use App\Services\Site\Pixel\DTO\PixelConversionDTO;
use App\Services\Site\Pixel\PixelService;

class SendPixelPostbackAfterEventAdded
{
    /**
     * Handle the event.
     */
    public function handle(PwaClientEventCreated $event): void
    {
        if (
            $event->pwaClientEvent->event !== Event::Deposit->value
            && $event->pwaClientEvent->event !== Event::Registration->value
        ) {
            return;
        }

        $click = $event->pwaClientEvent->pwaClientClick;

        if (!$click || !$click->fb_c || !$click->fb_p || !$click->pixel_id || !$click->pixel_key) {
            return;
        }

        try {
            /** @var PixelService $pixelService */
            $pixelService = app(PixelService::class);
            $pixelService->sendConversion(PixelConversionDTO::getFromClick($click, Event::mapEvent($event->pwaClientEvent->event)));
        } catch (\Exception $e) {
            logger()->driver('pixel')->error('Fail after send conversion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
    }
}
