<?php

namespace App\Services\Common\OneSignal\DTO\Interfaces;

interface ValidatableDTO
{
    public function validate(): void;
}
