<?php

namespace App\Services\Client\OnesignalTemplate\DTOs;

use App\Services\Client\OnesignalTemplate\SettingsInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SingleSettingsDTO implements SettingsInterface
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        public int $onesignalTemplateId,
        public ?string $scheduledAt,
        public ?string $handledAt = null,
    ) {
        $this->validate();
    }
    private function validationRules(): array
    {
        return [
            'onesignal_template_id' => ['required', 'integer', 'exists:onesignal_templates,id'],
            'scheduled_at' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after_or_equal:now']
        ];
    }

    public function validate(): void
    {
        Validator::make($this->toArray(), $this->validationRules())->validate();
    }

    /**
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['scheduled_at'],
            $data['onesignalTemplateId'],
            $data['handledAt'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'onesignal_template_id' => $this->onesignalTemplateId,
            'scheduled_at' => $this->scheduledAt,
            'handled_at' => $this->handledAt,
        ];
    }
}
