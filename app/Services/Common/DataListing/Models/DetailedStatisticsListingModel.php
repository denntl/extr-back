<?php

namespace App\Services\Common\DataListing\Models;

use App\Models\ApplicationStatistic;
use App\Services\Manage\Application\ApplicationService;
use App\Services\Common\DataListing\ListingFilterModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DetailedStatisticsListingModel extends ListingFilterModel
{
    public ?ApplicationStatistic $applicationStatistic = null;

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        if ($request->has('params.id')) {
            $params = $request->input('params');
            $id = $params['id'];
            $validationRules['params'] = 'array';
            $validationRules['params.id'] = 'integer|exists:application_statistics,id';
            $request->validate($validationRules);

            $this->applicationStatistic = ApplicationStatistic::query()
                ->where('id', $id)
                ->first();

            /** @var ApplicationService $applicationService */
            $applicationService = app(ApplicationService::class);
            $application = $applicationService->getById($this->applicationStatistic->application_id);
            Gate::authorize('readDetailedStatistics', [$application]);
        }
    }
}
