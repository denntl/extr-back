<?php

namespace App\Services\Client\OnesignalTemplate\DTOs;

use App\Services\Client\OnesignalTemplate\SettingsInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegularSettingsDTO implements SettingsInterface
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        public int $onesignalTemplateId,
        public ?string $time,
        public ?array $days = null,
        public ?string $handledAt = null,
    ) {
        $this->validate();
    }
    private function validationRules(): array
    {
        return [
            'onesignal_template_id' => ['required'],
            'time' => ['required'],
            'days' => ['required']
        ];
    }
    public function validate(): void
    {
        Validator::make($this->toArray(), $this->validationRules())->validate();
    }

    public function toArray(): array
    {
        return [
            'onesignal_template_id' => $this->onesignalTemplateId,
            'time' => $this->time,
            'days' => $this->days,
            'handled_at' => $this->handledAt,
        ];
    }
}
