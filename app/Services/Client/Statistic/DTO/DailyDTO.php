<?php

namespace App\Services\Client\Statistic\DTO;

readonly class DailyDTO
{
    public function __construct(private int $uniqueClicks, private int $installs, private int $registrations, private int $deposits)
    {
    }

    public function toArray(): array
    {
        return [
            'uniqueClicks' => $this->uniqueClicks,
            'installs' => $this->installs,
            'registrations' => $this->registrations,
            'deposits' => $this->deposits,
        ];
    }
}
