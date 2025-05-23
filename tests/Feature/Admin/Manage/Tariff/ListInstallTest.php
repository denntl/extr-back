<?php

namespace Feature\Admin\Manage\Tariff;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\Tariff\Enums\Type;
use Tests\TestCase;

class ListInstallTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ManageTariffUpdate);

        $expected = [
            [
                'value' => 1,
                'label' => 'Стандартный'
            ],
            [
                'value' => $company->tariff->id,
                'label' => $company->tariff->name,
            ]
        ];

        $response = $this->getRequest(
            route('manage.tariff.list', ['type' => Type::Install->value]),
            $token
        );

        $response->assertStatus(200);
        $this->assertEquals($expected, $response->json());
    }
}
