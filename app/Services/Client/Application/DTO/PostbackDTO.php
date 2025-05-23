<?php

namespace App\Services\Client\Application\DTO;

readonly class PostbackDTO
{
    public function __construct(public string $registration, public string $deposit)
    {
    }

    public function toArray(): array
    {
        return [
            'registration' => $this->registration,
            'deposit' => $this->deposit,
        ];
    }
}
