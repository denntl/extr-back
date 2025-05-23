<?php

namespace App\Services\Client\OnesignalTemplate\DTOs;

class TemplateToSendDTO
{
    public function __construct(
        public int $onesignal_templates_id,
        public string $name,
        public int $type,
        public bool $is_active,
        public array $segments,
        public int $geo_id,
        public string $geo_code,
        public string $title,
        public string $text,
        public string $big_image,
        public int $application_id,
        public string $scheduled_at,
    ) {
    }
}
