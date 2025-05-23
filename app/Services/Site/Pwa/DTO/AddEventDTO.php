<?php

namespace App\Services\Site\Pwa\DTO;

use App\Enums\PwaEvents\Event;
use App\Enums\PwaEvents\Platform;
use App\Services\Site\MobileDetect;

class AddEventDTO
{
    public function __construct(
        public string $clickExternalId,
        public Event|null $event,
        public array $details,
        public string $fullDomain,
        public array $geo,
        public Platform $platform
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            clickExternalId: $data['external_id'],
            event: isset($data['status']) ? Event::mapEvent($data['status']) : null,
            details: $data['details'] ?? [],
            fullDomain: $data['full_domain'] ?? '',
            geo: $data['geo'] ?? [],
            platform: self::getPlatform(), //Не используйте метод для миграций. Платформа определяется с реквеста
        );
    }

    public static function getPlatform(): Platform
    {
        $mobileDetectService = new MobileDetect();

        foreach (Platform::cases() as $case) {
            if ($mobileDetectService->is($case->value)) {
                return $case;
            }
        }

        return Platform::Unknown;
    }
}
