<?php

namespace Tests\Feature\Admin\Manage\Tariff;

use App\Enums\Authorization\PermissionName;
use Database\Factories\TariffFactory;
use Tests\TestCase;

class EditInstallTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageTariffUpdate);
        $tariff = TariffFactory::new()->install()->create()->load('tiers.countries');
        $expected = ['tiers' => []];
        foreach ($tariff->tiers->toArray() as $tier) {
            $expected['tiers'][$tier['id']] = [
                "name" => $tier['name'],
                "price" => floatval($tier['price'])
            ];
            foreach ($tier['countries'] as $country) {
                $expected['tiers'][$tier['id']]['countries'][] = $country['country'];
            }
            sort($expected['tiers'][$tier['id']]['countries']);
        }

        $response = $this->getRequest(
            route('manage.tariff.edit', ['id' => $tariff->id]),
            $token
        );

        $response->assertStatus(200);
        $this->assertEquals($expected, $response->json());
    }
}
