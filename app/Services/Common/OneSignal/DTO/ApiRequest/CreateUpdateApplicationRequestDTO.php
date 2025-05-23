<?php

namespace App\Services\Common\OneSignal\DTO\ApiRequest;

class CreateUpdateApplicationRequestDTO
{
    /**
     * @param string $sub
     * @param string $domain
     */
    public function __construct(public string $sub, public string $domain)
    {
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'name' => "{$this->sub}.{$this->domain}",
            'chrome_web_origin' => "https://{$this->sub}.{$this->domain}",
            'safari_site_origin' => "https://{$this->sub}.{$this->domain}",
            'site_name' => "{$this->sub} {$this->domain}",
        ];
    }
}
