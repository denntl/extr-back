<?php

namespace Feature\Admin\Manage\Tariff;

use App\Enums\Authorization\PermissionName;
use Tests\TestCase;

class ViewInstallTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientTariffRead);
        $expected = ['tiers' => []];
        foreach ($company->tariff->tiers->toArray() as $tier) {
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
            route('client.tariff.view'),
            $token
        );

        $response->assertStatus(200);
        $this->assertEquals($expected, $response->json());
    }
}
