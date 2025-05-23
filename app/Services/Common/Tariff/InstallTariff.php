<?php

namespace App\Services\Common\Tariff;

use App\Enums\Application\Geo;
use App\Models\Tariff;
use App\Models\Tier;
use App\Models\TierCountry;
use App\Rules\InEnum;
use App\Services\Common\Tariff\Enums\Type;
use App\Services\Common\Tariff\Interfaces\TariffInterface;
use Exception;

class InstallTariff implements TariffInterface
{
    /**
     * @param int $tariffId
     * @return array
     * @throws Exception
     */
    public function getObjectForEdit(int $tariffId): array
    {
        $this->refreshCountries($tariffId);

        return ['tiers' => Tier::with(['countries:country,tier_id'])
            ->where('tariff_id', $tariffId)
            ->select('id', 'name', 'price')
            ->get()
            ->mapWithKeys(function ($tier) {
                return [
                    $tier->id => [
                        'name' => $tier->name,
                        'price' => floatval($tier->price),
                        'countries' => collect($tier->countries->pluck('country')->toArray())->sort()->values()->toArray(),
                    ],
                ];
            })
            ->toArray()];
    }

    /**
     * @param Tariff $tariff
     * @return array
     */
    public function getObjectForView(Tariff $tariff): array
    {
        return ['tiers' => Tier::with(['countries:country,tier_id'])
            ->where('tariff_id', $tariff->id)
            ->select('id', 'name', 'price')
            ->get()
            ->mapWithKeys(function ($tier) {
                return [
                    $tier->id => [
                        'name' => $tier->name,
                        'price' => floatval($tier->price),
                        'countries' => collect($tier->countries->pluck('country')->toArray())->sort()->values()->toArray(),
                    ],
                ];
            })
            ->toArray()];
    }

    /**
     * @param int $tariffId
     * @return void
     * @throws Exception
     */
    public function refreshCountries(int $tariffId): void
    {
        $tierCountries = TierCountry::whereHas('tier', fn($query) =>
            $query->where('tariff_id', $tariffId))->pluck('country')->unique()->toArray();

        $geoCountries = array_map(fn($geo) => $geo->value, Geo::cases());
        $newCountries = array_diff($geoCountries, $tierCountries);

        if (!empty($newCountries)) {
            $tierId = Tier::where('tariff_id', $tariffId)
                ->latest('id')
                ->value('id');

            if (!$tierId) {
                throw new Exception('Tier not found');
            }

            $data = collect($newCountries)->map(fn($country) => [
                'tier_id' => $tierId,
                'country' => $country,
            ])->toArray();

            TierCountry::insert($data);
        }
    }

    /**
     * @return array
     */
    public function getListForSelections(): array
    {
        return Tariff::query()
            ->where('type_id', Type::Install->value)
            ->selectRaw('id as value, name as label')
            ->get()
            ->toArray();
    }

    /**
     * @param string $action
     * @return string[]
     * @throws Exception
     */
    public function getValidationRules(string $action): array
    {
        return match ($action) {
            TariffService::VALIDATION_UPDATE_RULE => [
                'tiers' => 'required|array',
                'tiers.*.name' => 'nullable|string',
                'tiers.*.price' => 'nullable|numeric|min:0.01|max:1',
                'tiers.*.countries.*' => [new InEnum(Geo::class)]
            ],
            default => throw new Exception('Validation rules not found'),
        };
    }

    /**
     * @param int $tariffId
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function update(int $tariffId, array $data): void
    {
        if (empty($data['tiers'])) {
            throw new Exception('Tiers not found');
        }
        foreach ($data['tiers'] as $tierId => $tierData) {
            $tier = Tier::where('id', $tierId)->where('tariff_id', $tariffId)->firstOrFail();
            $tier->update([
                'name' => $tierData['name'] ?? $tier->name,
                'price' => $tierData['price'] ?? $tier->price,
            ]);
            if (isset($tierData['countries'])) {
                $tier->countriesBelongsTo()->sync($tierData['countries']);
            }
        }
    }
}
