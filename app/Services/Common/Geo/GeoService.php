<?php

namespace App\Services\Common\Geo;

use App\Models\Geo;

class GeoService
{
    public static function getListForSelect(): array
    {
        return Geo::query()
            ->selectRaw('id as value, code as label')
            ->get()
            ->toArray();
    }
}
