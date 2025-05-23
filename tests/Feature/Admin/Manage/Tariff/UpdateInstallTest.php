<?php

namespace Tests\Feature\Admin\Manage\Tariff;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\Tariff\TariffService;
use Database\Factories\TariffFactory;
use Tests\TestCase;

class UpdateInstallTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageTariffUpdate);
        $tariff = TariffFactory::new()->install(['price' => 0.6])->create()->load('tiers.countries');
        $expected = ['tiers' => []];
        foreach ($tariff->tiers->toArray() as $tier) {
            $expected['tiers'][$tier['id']] = [
                "name" => $tier['name'],
                "price" => 0.3
            ];
            foreach ($tier['countries'] as $country) {
                $expected['tiers'][$tier['id']]['countries'][] = $country['country'];
            }
            sort($expected['tiers'][$tier['id']]['countries']);
        }

        $response = $this->postRequest(
            route('manage.tariff.update', ['id' => $tariff->id]),
            $expected,
            $token
        );

        $tariffService = app(TariffService::class, ['tariff_id' => $tariff->id]);
        $tariffUpdated = $tariffService->getObjectForEdit($tariff->id);

        $response->assertStatus(200);
        $this->assertEquals($expected, $tariffUpdated);
    }

    public function testFailValidation()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageTariffUpdate);
        $tariff = TariffFactory::new()->install(['price' => 0.6])->create()->load('tiers.countries');
        $expected = ['tiers' => []];
        foreach ($tariff->tiers->toArray() as $tier) {
            $expected['tiers'][$tier['id']] = [
                "name" => $tier['name'],
                "price" => 'invalid value'
            ];
            foreach ($tier['countries'] as $country) {
                $expected['tiers'][$tier['id']]['countries'][] = $country['country'];
            }
            sort($expected['tiers'][$tier['id']]['countries']);
        }

        $response = $this->postRequest(
            route('manage.tariff.update', ['id' => $tariff->id]),
            $expected,
            $token
        );

        $response->assertStatus(422);
    }
}
