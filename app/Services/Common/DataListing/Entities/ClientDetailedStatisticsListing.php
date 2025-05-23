<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\PwaClientEvent;
use App\Services\Client\Application\ApplicationService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldDateTime;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldListAsString;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Common\DataListing\Models\DetailedStatisticsListingModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @param DetailedStatisticsListingModel $listingModel
 */
class ClientDetailedStatisticsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new PwaClientEvent();
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
                FieldDateTime::init('date', 'pwa_client_events.created_at', 'Дата')
                    ->makeUnSearchable()
                    ->makeUnSortable(),
                FieldString::init('full_domain', 'pwa_client_events.full_domain', 'Домен')
                    ->makeUnSearchable()
                    ->makeUnSortable(),
                FieldListAsString::init('geo', 'pwa_client_events.geo', 'Гео')
                    ->setListName('geos')
                    ->setCustomStyles(['minWidth' => '200px'])
                    ->makeUnSearchable()
                    ->makeUnSortable(),
                FieldList::init('event', 'pwa_client_events.event', 'Событие')
                    ->setListName('events'),
                FieldList::init('platform', 'pwa_client_events.platform', 'Платформа')
                    ->setListName('platforms'),
                FieldString::init('external_id', 'pwa_client_clicks.external_id', 'External ID')
                    ->withScope('clientClick'),
                FieldString::init('user_id', 'pwa_clients.external_id', 'Идентификатор пользователя')
                    ->withScope('client'),
                FieldInt::init('app_id', 'pwa_clients.application_id', 'App ID')
                    ->hideInSelect(),
                FieldInt::init('company_id', 'applications.company_id')
                    ->withScope('application')
                    ->hideInSelect(),
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('date', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        return [
            'geos' => ApplicationService::getGeoList(),
            'platforms' => ApplicationService::getPWAStatisticsPlatformList(),
            'events' => ApplicationService::getStatisticEventList(),
        ];
    }

    /**
     * @return array|FilterDTO[]
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {
        if (empty($this->listingModel->applicationStatistic)) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, -1),
            ];
        }
        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            new FilterDTO('date', FilterOperator::Between, [
                Carbon::createFromDate($this->listingModel->applicationStatistic->date)->startOfDay(),
                Carbon::createFromDate($this->listingModel->applicationStatistic->date)->endOfDay(),
            ]),
        ];
    }
}
