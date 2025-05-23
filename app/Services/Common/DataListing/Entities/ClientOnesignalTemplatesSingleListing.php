<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\OnesignalTemplate;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\OnesignalTemplate\OnesignalTemplateService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldListAsString;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Common\Geo\GeoService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ClientOnesignalTemplatesSingleListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new OnesignalTemplate();
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()->setBatch([
            FieldInt::init('id', 'onesignal_templates.id', 'ID')->makeInvisible(),
            FieldString::init('name', 'onesignal_templates.name', 'Название'),
            FieldString::init('creator', 'users.username', 'Пользователь')
                ->withScope('createdBy')
                ->setListName('users'),
            FieldListAsString::init('application', 'array_agg(DISTINCT applications.full_domain)', 'Приложения')
                ->setTransform(function ($e) {
                    return str_replace(',', ', ', (str_replace(['{', '}'], '', $e)));
                })
                ->withScope('applications'),
            FieldListAsString::init('segments', 'onesignal_templates.segments', 'Сегменты')
                ->setListName('segments'),
            FieldListAsString::init('geo', 'array_agg(DISTINCT geos.code)', 'Гео')
                ->setTransform(function ($e) {
                    return str_replace(',', ', ', (str_replace(['{', '}'], '', $e)));
                })
                ->withScope('geos')
                ->setGroupBy(['onesignal_templates.id', 'users.username']),

            FieldList::init('type', 'onesignal_templates.type', 'Тип')
                ->setListName('types'),
            FieldList::init(
                'scheduled_at',
                'CASE
                        WHEN onesignal_templates.type = 1
                            THEN onesignal_templates_single_settings.scheduled_at::TEXT
                        WHEN onesignal_templates.type = 2
                            THEN CONCAT(onesignal_templates_regular_settings.time::TEXT)
                        WHEN onesignal_templates.type = 3
                            THEN onesignal_templates_delayed_settings.delay::TEXT
                        END',
                'Время отправки'
            )
                ->withScope('settings')
                ->setGroupBy([
                    'onesignal_templates_single_settings.scheduled_at',
                    'onesignal_templates_regular_settings.time',
                    'onesignal_templates_delayed_settings.delay'
                ]),
            FieldList::init('status', 'onesignal_templates.is_active', 'Статус')
                ->setListName('statuses'),
            FieldAction::init('actions', '', 'Действия'),
        ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getPermission(): array
    {
        return [PermissionName::ClientPushNotificationRead];
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return [
            'geos' => GeoService::getListForSelect(),
            'users' => $userService->getUsersForSelections(),
            'segments' => OnesignalTemplateService::getSegmentsList(),
            'types' => OnesignalTemplateService::getTypesList(),
            'statuses' => [
                ['value' => false, 'label' => 'Неактивный'],
                ['value' => true, 'label' => 'Активный']
            ],
        ];
    }

    protected function predefinedFilters(): array
    {
        $commonFilter = [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
        ];

        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);
        /** @var OnesignalTemplateService $templateService */
        $templateService = app(OnesignalTemplateService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return $commonFilter;
        }

        $applications = $applicationService->getTeamsOrOwnApplications($this->listingModel->user->id)->pluck('id')->toArray();
        $templates = $templateService->getTeamsOrOwnTemplates($applications)->pluck('id')->toArray();
        return array_merge($commonFilter, [
            new FilterDTO('id', FilterOperator::In, $templates),
        ]);
    }
}
