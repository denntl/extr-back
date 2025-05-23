<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Models\Tariff;
use App\Models\Tier;
use App\Models\TierCountry;
use App\Services\Common\Tariff\Enums\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TariffFactory extends Factory
{
    protected $model = Tariff::class;

    public function definition()
    {
        return [
            'name' => Str::random(10),
            'type_id' => Type::Install->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param array $data
     * @return TariffFactory
     */
    public function install(array $data = []): TariffFactory
    {
        return $this->afterCreating(function (Tariff $tariff) use ($data){
            $tiers = Tier::factory()
                ->count(4)
                ->sequence(
                    ['tariff_id' => $tariff->id, 'price' => $data['price'] ?? 0.1, 'name' => 'Тир 1'],
                    ['tariff_id' => $tariff->id, 'price' => $data['price'] ?? 0.2, 'name' => 'Тир 2'],
                    ['tariff_id' => $tariff->id, 'price' => $data['price'] ?? 0.3, 'name' => 'Тир 3'],
                    ['tariff_id' => $tariff->id, 'price' => $data['price'] ?? 0.4, 'name' => 'Тир 4']
                )
                ->create();
            $geos = Geo::cases();
            $tierCount = count($tiers);
            /** @var Tier $tier */
            foreach ($tiers as $key => $tier) {
                if ($tierCount === $key + 1) {
                    $remainingGeos = array_slice($geos, $key);
                    $data = [];
                    foreach ($remainingGeos as $geo) {
                        $data[] = ['tier_id' => $tier->id, 'country' => $geo->value];
                    }
                    TierCountry::insert($data);
                } else {
                    TierCountry::factory()
                        ->create(['tier_id' => $tier->id, 'country' => $geos[$key]->value]);
                }
            }
        });
    }
}
