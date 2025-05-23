<?php

namespace App\Services\Common\DataListing;

use App\Enums\DataListing\EntityName;
use App\Services\Common\DataListing\Models\DetailedCompanyBalanceTransactionsListingModel;
use App\Services\Common\DataListing\Models\DetailedStatisticsListingModel;
use Exception;
use Illuminate\Http\Request;

class FilterModelFactory
{
    /**
     * @param EntityName $entity
     * @param Request $request
     * @return ListingFilterModel
     * @throws Exception
     */
    public static function init(EntityName $entity, Request $request): ListingFilterModel
    {
        return match ($entity) {
            EntityName::DetailedStatistics => new DetailedStatisticsListingModel($request),
            EntityName::ManageCompanyBalanceTransactions => new DetailedCompanyBalanceTransactionsListingModel($request),
            default => new ListingFilterModel($request),
        };
    }
}
