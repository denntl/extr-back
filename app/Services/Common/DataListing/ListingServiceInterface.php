<?php

namespace App\Services\Common\DataListing;

interface ListingServiceInterface
{
    public function getListingData(): array;

    public function getSettings(): array;

    public function getAggregations(): array;
}
