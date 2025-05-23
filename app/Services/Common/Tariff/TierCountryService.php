<?php

namespace App\Services\Common\Tariff;

use Illuminate\Support\Facades\DB;

class TierCountryService
{
    /**
     * @param int $companyTariffId
     * @param string $country
     * @return float
     */
    public function getTierPriceByTariffIdCountry(int $companyTariffId, string $country): float
    {
        $tierPrice = DB::table('tier_countries')
            ->join('tiers', 'tier_countries.tier_id', '=', 'tiers.id')
            ->where('tiers.tariff_id', $companyTariffId)
            ->where('tier_countries.country', $country)
            ->value('tiers.price');

        if (empty($tierPrice)) {
            return DB::table('tiers')
                ->where('tariff_id', $companyTariffId)
                ->where('name', 'Тир 4')
                ->value('price');
        }

        return $tierPrice;
    }
}
