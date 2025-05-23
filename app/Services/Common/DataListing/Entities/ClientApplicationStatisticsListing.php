<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\User\UserService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\Aggregation;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldPercent;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;

class ClientApplicationStatisticsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new ApplicationStatistic();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientApplicationStatisticRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'application_statistics.id', 'id')
                    ->makeUnSortable()
                    ->makeInvisible()
                    ->makeUnSearchable(),
                FieldDate::init('date', 'application_statistics.date', 'Дата'),
                FieldString::init('domain', 'applications.full_domain', 'Домен')->withScope('application'),

                FieldList::init('geos', 'app_geos.geos', 'Гео')
                    ->setFilterField('application_geo_languages.geo')
                    ->setListName('geos')
                    ->setCustomStyles(['minWidth' => '200px'])->withScope('geo'),
                FieldList::init('owner', 'users.username', 'Пользователь')
                    ->withScope('owner')
                    ->setFilterField('companies.owner_id')
                    ->setListName('users'),
                FieldInt::init('clicks', 'application_statistics.clicks', 'Клики')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('push_subscriptions', 'application_statistics.push_subscriptions', 'PUSH подписки')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('unique_clicks', 'application_statistics.unique_clicks', 'Уникальные')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('installs', 'application_statistics.installs', 'Инсталлы')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('opens', 'application_statistics.opens', 'Открытия')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('first_installs', 'application_statistics.first_installs', 'Первые инсталлы')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('first_opens', 'application_statistics.first_opens', 'Первые открытия')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('repeated_installs', 'application_statistics.repeated_installs', 'Повторные инсталлы')
                    ->setAggregation(Aggregation::Sum),
                FieldInt::init('repeated_opens', 'application_statistics.repeated_opens', 'Повторные открытия')
                    ->setAggregation(Aggregation::Sum),
                FieldPercent::init('ins_to_uc', 'application_statistics.ins_to_uc', 'CR to install (unique)'),
                FieldInt::init('registrations', 'application_statistics.registrations', 'Регистрации')
                    ->setAggregation(Aggregation::Sum),
                FieldPercent::init('reg_to_ins', 'application_statistics.reg_to_ins', 'CR to reg (install)'),
                FieldInt::init('deposits', 'application_statistics.deposits', 'Депозиты')
                    ->setAggregation(Aggregation::Sum),
                FieldPercent::init('dep_to_ins', 'application_statistics.dep_to_ins', 'CR to dep (install)'),
                FieldPercent::init('dep_to_reg', 'application_statistics.dep_to_reg', 'CR to dep (reg)'),
                FieldInt::init('company_id', 'applications.company_id')->hideInSelect(),
                FieldInt::init('app_id', 'applications.id')->hideInSelect(),
                FieldAction::init('actions', '', 'Действия'),
            ])->distinct();
    }

    protected function decorateAggregations(array $data): array
    {
        $data['ins_to_uc']['label'] = !empty($data['unique_clicks']['label'])
            ? round(100 * $data['installs']['label'] / $data['unique_clicks']['label'], 2) . ' %'
            : 0;
        $data['reg_to_ins']['label'] = !empty($data['installs']['label'])
            ? round(100 * $data['registrations']['label'] / $data['installs']['label'], 2) . ' %'
            : 0;
        $data['dep_to_ins']['label'] = !empty($data['installs']['label'])
            ? round(100 * $data['deposits']['label'] / $data['installs']['label'], 2) . ' %'
            : 0;
        $data['dep_to_reg']['label'] = !empty($data['registrations']['label'])
            ? round(100 * $data['deposits']['label'] / $data['registrations']['label'], 2) . ' %'
            : 0;
        return $data;
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('date', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);

        return [
            'users' => $userService->getUsersForSelections(),
            'geos' => ApplicationService::getGeoList(),
        ];
    }

    /**
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            ];
        }

        $applications = $applicationService->getTeamsOrOwnApplications($this->listingModel->user->id)->pluck('id')->toArray();
        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            new FilterDTO('app_id', FilterOperator::In, $applications),
        ];
    }
}
