<?php

namespace App\Services\Common\OneSignal\DTO\Webhook;

use App\Enums\OneSignal\NotificationEventId;
use App\Enums\OneSignal\NotificationEventType;

class WebhookDTO
{
    public static function fromArray(array $data): self
    {
        $eventId = match ($data['event']) {
            NotificationEventType::Display->value => NotificationEventId::Display,
            NotificationEventType::Clicked->value => NotificationEventId::Clicked,
            NotificationEventType::Dismissed->value => NotificationEventId::Dismissed,
            default => throw new \InvalidArgumentException('Invalid event'),
        };

        return new self($eventId, $data['notificationId']);
    }

    public function __construct(public NotificationEventId $eventId, public string $notificationId)
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId->value,
            'notification_id' => $this->notificationId,
        ];
    }
}
