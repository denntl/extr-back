<?php

namespace App\Services\Client\OnesignalTemplate;

use App\Models\Application;
use App\Models\OnesignalTemplate;
use App\Services\Client\OnesignalTemplate\DTOs\PushRequest\SendSingleRequestDTO;
use App\Services\Client\OnesignalTemplate\DTOs\TemplateToSendDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractDelivery
{
    abstract protected function settingsQuery(Builder $query): Builder;
    abstract public function getSettingsToPush(): Builder;
    abstract public function sendRequestDTO(TemplateToSendDTO $template, Application $application): SendSingleRequestDTO;
    abstract protected function getSettingsModel(): Model;
    abstract public function convertTemplateToDto($template): TemplateToSendDTO;

    public function setHandled(int $templateId): void
    {
        $this->getSettingsModel()->query()->where('onesignal_template_id', $templateId)
            ->update(['handled_at' => Carbon::now('UTC')]);
    }

    public function getAllTemplatesById(int $templateId): Collection
    {
        $query = OnesignalTemplate::query()
            ->select(
                'onesignal_templates.id as onesignal_templates_id',
                'onesignal_templates.name',
                'onesignal_templates.type',
                'onesignal_templates.is_active',
                'onesignal_templates.segments',
                'onesignal_templates_contents.geo_id',
                'geos.code as geo_code',
                'onesignal_templates_contents.title',
                'onesignal_templates_contents.text',
                'onesignal_templates_contents.image as big_image',
                'onesignal_templates_applications.application_id',
            )
            ->join('onesignal_templates_contents', 'onesignal_templates_contents.onesignal_template_id', '=', 'onesignal_templates.id')
            ->join('onesignal_templates_applications', 'onesignal_templates.id', '=', 'onesignal_templates_applications.onesignal_template_id')
            ->join('geos', 'onesignal_templates_contents.geo_id', '=', 'geos.id')
            ->where('onesignal_templates.is_active', '=', true);

        $query = $this->settingsQuery($query);

        if (is_array($templateId)) {
            $query->whereIn('onesignal_templates.id', $templateId);
        } else {
            $query->where('onesignal_templates.id', '=', $templateId);
        }

        return $query->get();
    }
}
