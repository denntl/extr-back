<?php

namespace App\Services\Common\Invite\DTO;

class InviteDTO
{
    public function __construct(protected string $key, protected string $expiredAt, protected string $companyName)
    {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'expire' => $this->expiredAt,
            'companyName' => $this->companyName,
        ];
    }
}
