<?php

namespace Database\Seeders;

use App\Enums\Application\Geo;
use App\Models\Geo as ModelGeo;
use Illuminate\Database\Seeder;

class GeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Geo::cases() as $geo) {
            ModelGeo::query()->updateOrCreate([
                'code'       => $geo->value,
            ], [
                'code'       => $geo->value,
            ]);
        }
    }
}
