<?php

namespace App\Services\Common\OneSignal\DTO\ApiRequest;

use App\Services\Common\OneSignal\Enums\IpAllowlistMode;

class CreateApiKeyRequestDTO
{
    /**
     * @param string $name
     * @param IpAllowlistMode $ip_allowlist_mode
     * @param array<string>|null $ip_allowlist
     */
    public function __construct(
        public string $name,
        public IpAllowlistMode $ip_allowlist_mode = IpAllowlistMode::DISABLED,
        public ?array $ip_allowlist = null,
    ) {
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'ip_allowlist_mode' => $this->ip_allowlist_mode,
            'ip_allowlist' => $this->ip_allowlist,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            ip_allowlist_mode: $data['ip_allowlist_mode'] ?? IpAllowlistMode::DISABLED,
            ip_allowlist: $data['ip_allowlist'] ?? null,
        );
    }
}
