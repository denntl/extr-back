<?php

namespace App\Services\Client\OnesignalTemplate\DeliverySettings;

use App\Models\Application;
use App\Models\OnesignalTemplateSingleSettings;
use App\Services\Client\OnesignalTemplate\AbstractDelivery;
use App\Services\Client\OnesignalTemplate\DTOs\PushRequest\SendSingleRequestDTO;
use App\Services\Client\OnesignalTemplate\DTOs\TemplateToSendDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SingleDeliveryService extends AbstractDelivery
{
    protected function getSettingsModel(): OnesignalTemplateSingleSettings
    {
        return new OnesignalTemplateSingleSettings();
    }

    protected function settingsQuery(Builder $query): Builder
    {
        $query->addSelect(
            'onesignal_templates_single_settings.scheduled_at',
            'onesignal_templates_single_settings.handled_at'
        );
        $query->join('onesignal_templates_single_settings', 'onesignal_templates_single_settings.onesignal_template_id', '=', 'onesignal_templates.id');
        return $query;
    }

    public function getSettingsToPush(): Builder
    {
        return OnesignalTemplateSingleSettings::query()
            ->whereBetween('scheduled_at', [Carbon::now('UTC'), Carbon::now('UTC')->addMinutes(5)])
            ->whereNull('handled_at');
    }

    /**
     * @throws InvalidPushNotificationArgumentException
     */
    public function sendRequestDTO(TemplateToSendDTO $template, Application $application): SendSingleRequestDTO
    {
        return new SendSingleRequestDTO(
            url: $application->link,
            title: $template->title,
            contents: $template->text,
            bigPicture: $template->big_image,
            smallIcon: $application->icon,
            segments: $template->segments,
            events: $template->segments,
            geos: [$template->geo_code],
            sendAfter: $template->scheduled_at,
        );
    }

    public function convertTemplateToDto($template): TemplateToSendDTO
    {
        return new TemplateToSendDTO(
            onesignal_templates_id: $template->onesignal_templates_id,
            name: $template->name,
            type: $template->type,
            is_active: $template->is_active,
            segments: $template->segments,
            geo_id: $template->geo_id,
            geo_code: $template->geo_code,
            title: $template->title,
            text: $template->text,
            big_image: $template->big_image,
            application_id: $template->application_id,
            scheduled_at: $template->scheduled_at,
        );
    }
}
