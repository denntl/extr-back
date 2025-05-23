<?php

namespace App\Services\Site\Pixel\DTO;

use App\Enums\PwaEvents\Event;
use App\Models\PwaClientClick;
use http\Exception\InvalidArgumentException;

class PixelConversionDTO
{
    public function __construct(
        public string $pixelId,
        public string $pixelToken,
        public string $eventName,
        public string $clientIpAddress,
        public string $userAgent,
        public string $fbc,
        public string $fbp,
        public string $externalId,
        public string $sourceUrl,
        public string $sourceAction = 'website'
    ) {
    }

    public static function getFromClick(PwaClientClick $click, Event $event): self
    {
        if ($event !== Event::Deposit && $event !== Event::Registration) {
            throw new \InvalidArgumentException("Event $event->value is not supported");
        }
        $application = $click->pwaClient->application;

        $fbEvent = match ($event) {
            Event::Deposit => 'Purchase',
            Event::Registration => 'CompleteRegistration',
            default => throw new InvalidArgumentException($event->value)
        };

        return new self(
            pixelId: $click->pixel_id,
            pixelToken: $click->pixel_key,
            eventName: $fbEvent,
            clientIpAddress: $click->ip,
            userAgent: $click->useragent,
            fbc: $click->fb_c,
            fbp: $click->fb_p,
            externalId: $click->external_id,
            sourceUrl: "https://$application->full_domain",
        );
    }
}
