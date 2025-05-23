<?php

namespace App\Services\Client\OnesignalTemplate\DTOs;

use App\Enums\PushNotification\Type;
use App\Services\Client\OnesignalTemplate\SettingsInterface;
use Illuminate\Validation\ValidationException;

class RequestDTO
{
    public ?int $delay = null;
    public ?int $event = null;
    public ?string $time = null;
    public ?array $days = null;
    public ?string $scheduled_at = null;
    public ?int $onesignal_template_id = null;
    public ?int $created_by;
    public string $name;
    public int $type;
    public bool $is_active;
    public array $segments;
    public array $geos;
    public array $application_ids;
    public array $contents;
    public SettingsInterface $settings;

    public function __construct(array $request, ?int $created_by = null, ?int $onesignal_template_id = null)
    {
        $this->onesignal_template_id = $onesignal_template_id;
        $this->name = $request['name'];
        $this->type = $request['type'];
        $this->is_active = $request['is_active'];
        $this->segments = $request['segments'];
        $this->geos = $request['geos'];
        $this->application_ids = $request['application_ids'];
        $this->created_by = $created_by ?? null;

        $this->contents = RequestContentsDTO::fromArray($request['contents']);

        $this->scheduled_at = $request['scheduled_at'] ?? null;
        $this->time = $request['time'] ?? null;
        $this->days = $request['days'] ?? null;
    }

    public function setTemplateId(int $onesignal_template_id): void
    {
        $this->onesignal_template_id = $onesignal_template_id;
    }

    public function commonData(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'segments' => $this->segments,
            'created_by' => $this->created_by,
        ];
    }

    /**
     * @throws \Exception
     */
    public function contentData(RequestContentsDTO $content, ?int $onesignal_template_id = null): array
    {
        if (empty($this->onesignal_template_id) && empty($onesignal_template_id)) {
            throw new \Exception("Onesignal template id is required to update contents");
        }

        return [
            'onesignal_template_id' => $onesignal_template_id ?? $this->onesignal_template_id,
            'geo_id' => $content->geo,
            'title' => $content->title,
            'text' => $content->text,
            'image' => $content->image,
        ];
    }
}
