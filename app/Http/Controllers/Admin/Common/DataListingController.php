<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Services\Common\DataListing\ListingServiceInterface;
use Illuminate\Http\JsonResponse;

class DataListingController extends Controller
{
    public function settings(ListingServiceInterface $listingService): JsonResponse
    {
        return response()->json($listingService->getSettings());
    }

    public function data(ListingServiceInterface $listingService): JsonResponse
    {
        return response()->json($listingService->getListingData());
    }

    public function aggregations(ListingServiceInterface $listingService): JsonResponse
    {
        return response()->json($listingService->getAggregations());
    }
}
