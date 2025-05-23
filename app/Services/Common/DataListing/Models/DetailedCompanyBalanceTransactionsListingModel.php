<?php

namespace App\Services\Common\DataListing\Models;

use App\Services\Common\DataListing\ListingFilterModel;
use Exception;
use Illuminate\Http\Request;

/**
 * @param DetailedCompanyBalanceTransactionsListingModel $listingModel
 */
class DetailedCompanyBalanceTransactionsListingModel extends ListingFilterModel
{
    public ?int $companyId = null;

    /**
     * @param Request $request
     * @throws Exception
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        if ($request->has('companyId')) {
            $companyId = (int) $request->input('companyId');
            $validationRules['companyId'] = 'integer|exists:companies,id';
            $request->validate($validationRules);

            $this->companyId = $companyId;
        }
    }
}
