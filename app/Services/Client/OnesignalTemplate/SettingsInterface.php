<?php

namespace App\Services\Client\OnesignalTemplate;

use Illuminate\Validation\ValidationException;

interface SettingsInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(): void;
    public function toArray(): array;
}
