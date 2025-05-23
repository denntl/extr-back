<?php

namespace App\Services\Client\OnesignalTemplate\Settings;

use App\Models\OnesignalTemplateSingleSettings;
use App\Services\Client\OnesignalTemplate\AbstractTemplate;
use App\Services\Client\OnesignalTemplate\DTOs\RequestDTO;
use App\Services\Client\OnesignalTemplate\DTOs\SingleSettingsDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class SingleTemplateService extends AbstractTemplate
{
    protected function getSettingsModel(): OnesignalTemplateSingleSettings
    {
        return new OnesignalTemplateSingleSettings();
    }
    protected function getSettingsModelByTemplateId(int $templateId): Model
    {
        return $this->getSettingsModel()->query()->where('onesignal_template_id', $templateId)->firstOrFail();
    }

    /**
     * @throws ValidationException
     */
    protected function getSettingsDTO(RequestDTO $requestDTO): SingleSettingsDTO
    {
        return new SingleSettingsDTO($requestDTO->onesignal_template_id, $requestDTO->scheduled_at);
    }
}
