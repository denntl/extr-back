<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use Database\Seeders\PermissionSeeder;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleCreate);

        $this->seed(PermissionSeeder::class);

        $response = $this->getRequest(
            route('manage.roles.create'),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'permissions' => [
                '*' => [
                    'value',
                    'label',
                ]
            ],
        ]);
        $this->assertNotEmpty($response->json('permissions'));
    }
}
