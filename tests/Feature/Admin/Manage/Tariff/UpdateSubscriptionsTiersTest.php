<?php

namespace Tests\Feature\Admin\Manage\Tariff;

use App\Enums\Authorization\PermissionName;
use App\Enums\Tariff\Status;
use App\Enums\Tariff\Type;
use App\Models\Tariff;
use App\Services\Common\Tariff\TariffService;
use App\Services\Common\Tariff\TierService;
use Database\Factories\TariffFactory;
use Tests\TestCase;

class UpdateSubscriptionsTiersTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageTariffUpdate);
        $tariff = TariffFactory::new(
            ['type_id' => Type::SubscriptionTiers->value, 'amount' => 100.25, 'status' => 1]
        )->install()->create()->load('tiers.countries');
//        $tariff = TariffFactory::new(['amount' => 666])->install(['price' => 0.6])->create()->load('tiers.countries');
        $expectedTariff = $tariff->toArray();
        $expectedTariff['amount'] = 777;
        $expectedTiers = [];
        $tiers = $tariff->tiers->toArray();

        foreach ($tiers as $key => $tier) {
            $expectedTiers[$key] = [
                "name" => $tier['name'],
                "price" => 0.3,
                'countries' => []
            ];
        }

        $response = $this->postRequest(
            route('manage.tariff.update', ['id' => $tariff->id]),
            [
                'tariff' => $expectedTariff,
                'tiers' => $expectedTiers,
            ],
            $token
        );
        $response->assertStatus(200);

        $tariff = Tariff::query()->find($tariff->id);
        $this->assertEquals($expectedTariff['amount'], $tariff->amount);
    }

//    public function testFailValidation()
//    {
//        [$token, $user] = $this->getUserToken(PermissionName::ManageTariffUpdate);
//        $tariff = TariffFactory::new()->install(['price' => 0.6])->create()->load('tiers.countries');
//        $expected = ['tiers' => []];
//        foreach ($tariff->tiers->toArray() as $tier) {
//            $expected['tiers'][$tier['id']] = [
//                "name" => $tier['name'],
//                "price" => 'invalid value'
//            ];
//            foreach ($tier['countries'] as $country) {
//                $expected['tiers'][$tier['id']]['countries'][] = $country['country'];
//            }
//            sort($expected['tiers'][$tier['id']]['countries']);
//        }
//
//        $response = $this->postRequest(
//            route('manage.tariff.update', ['id' => $tariff->id]),
//            $expected,
//            $token
//        );
//
//        $response->assertStatus(422);
//    }
}
