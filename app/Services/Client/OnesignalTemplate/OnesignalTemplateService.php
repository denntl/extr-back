<?php

namespace App\Services\Client\OnesignalTemplate;

use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateContents;
use App\Services\Client\OnesignalTemplate\DTOs\RequestDTO;
use App\Services\Client\OnesignalTemplate\Settings\SingleTemplateService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OnesignalTemplateService
{
    public function __construct(protected int $companyId)
    {
    }

    public static function getEventsList(): array
    {
        return [
            ['value' => Event::INSTALL, 'label' => 'Установка'],
            ['value' => Event::REGISTRATION, 'label' => 'Регистрация'],
            ['value' => Event::DEPOSIT, 'label' => 'Депозит'],
        ];
    }
    public static function getSegmentsList(): array
    {
        return [
            ['value' => Event::INSTALL, 'label' => 'Установка'],
            ['value' => Event::REGISTRATION, 'label' => 'Регистрация'],
            ['value' => Event::DEPOSIT, 'label' => 'Депозит'],
        ];
    }
    public static function getTypesList(): array
    {
        return [
            ['value' => Type::Single, 'label' => 'Одноразовая'],
            ['value' => Type::Regular, 'label' => 'Регулярная'],
        ];
    }

    private function getSettingService(Type $type): AbstractTemplate
    {
        return match ($type) {
            Type::Single => new SingleTemplateService(),
            Type::Regular => throw new \Exception('To be implemented'),
//            Type::Regular->value => OnesignalTemplateRegularSettings::class,
        };
    }

    /**
     * @throws \Throwable
     * @throws ValidationException
     */
    public function store(RequestDTO $data): OnesignalTemplate
    {
        $type = Type::tryFrom($data->type);
        return $this->getSettingService($type)->store($data);
    }

    /**
     * @throws \Throwable
     * @throws ValidationException
     */
    public function update(RequestDTO $data): int
    {
        $template = $this->getTemplateModelById($data->onesignal_template_id);
        $type = Type::tryFrom($data->type);
        return $this->getSettingService($type)->update($data, $template);
    }

    /**
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $template = $this->getTemplateModelById($id);
        $type = Type::tryFrom($template->type);
        $this->getSettingService($type)->delete($id, $template);
    }

    public function getTemplateModelById(int $templateId): OnesignalTemplate
    {
        return OnesignalTemplate::query()
            ->select('onesignal_templates.*')
            ->join('onesignal_templates_applications', 'onesignal_templates_applications.onesignal_template_id', '=', 'onesignal_templates.id')
            ->join('applications', 'onesignal_templates_applications.application_id', '=', 'applications.id')
            ->where('onesignal_templates.id', $templateId)
            ->where('applications.company_id', $this->companyId)
            ->firstOrFail();
    }

    public function getTemplateById(int $id): OnesignalTemplate
    {
        $query = OnesignalTemplate::query()
            ->select([
                'onesignal_templates.id as id',
                'onesignal_templates.name as name',
                DB::raw('ARRAY_AGG(DISTINCT applications.public_id) as application_ids'),
                'onesignal_templates.segments as segments',
                DB::raw('ARRAY_AGG(DISTINCT geo_id) as geos'),
                'onesignal_templates.type as type',
                'onesignal_templates_single_settings.scheduled_at as scheduled_at',
                'onesignal_templates.is_active as is_active',
                'onesignal_templates_single_settings.scheduled_at',
                'onesignal_templates_regular_settings.time',
                'onesignal_templates_regular_settings.days',
                'onesignal_templates_delayed_settings.delay',
                'onesignal_templates_delayed_settings.event',
            ])
            ->join('onesignal_templates_applications', 'onesignal_templates_applications.onesignal_template_id', '=', 'onesignal_templates.id')
            ->join('applications', 'onesignal_templates_applications.application_id', '=', 'applications.id')
            ->join('onesignal_templates_contents', 'onesignal_templates.id', '=', 'onesignal_templates_contents.onesignal_template_id')
            ->leftJoin('onesignal_templates_single_settings', 'onesignal_templates_single_settings.onesignal_template_id', '=', 'onesignal_templates.id')
            ->leftJoin('onesignal_templates_regular_settings', 'onesignal_templates_regular_settings.onesignal_template_id', '=', 'onesignal_templates.id')
            ->leftJoin('onesignal_templates_delayed_settings', 'onesignal_templates_delayed_settings.onesignal_template_id', '=', 'onesignal_templates.id')
            ->where('onesignal_templates.id', $id)
            ->where('applications.company_id', $this->companyId)
            ->groupBy([
                'onesignal_templates.id',
                'onesignal_templates_single_settings.scheduled_at',
                'onesignal_templates_regular_settings.id',
                'onesignal_templates_single_settings.id',
                'onesignal_templates_delayed_settings.id',
            ]);

        $template = $query->first();
        $template->application_ids = array_map(fn($item) => (int) $item, $this->arrayAggToArray($template->application_ids));
        $template->geos = array_map(fn($item) => (int) $item, $this->arrayAggToArray($template->geos));
        return $template;
    }
    private function arrayAggToArray($arrayAgg): array
    {
        $arrayAgg = str_replace(['{', '}'], '', $arrayAgg);
        return explode(',', $arrayAgg);
    }
    public function getContentsByTemplateId(int $templateId): Collection
    {
        $query = OnesignalTemplateContents::query()
            ->select([
                'geo_id as geo',
                'code',
                'title',
                'text',
                'image'
            ])
            ->join('geos', 'onesignal_templates_contents.geo_id', '=', 'geos.id')
            ->where('onesignal_template_id', $templateId);
        return $query->get();
    }

    public function getTeamsOrOwnTemplates(array $teamsOrOwnApps): Collection
    {
        return OnesignalTemplate::query()
            ->select('onesignal_templates.id')
            ->join('onesignal_templates_applications', 'onesignal_templates_applications.onesignal_template_id', '=', 'onesignal_templates.id')
            ->join('applications', 'onesignal_templates_applications.application_id', '=', 'applications.id')
            ->whereIn('onesignal_templates_applications.application_id', $teamsOrOwnApps)
            ->where('applications.company_id', $this->companyId)
            ->get();
    }
}
