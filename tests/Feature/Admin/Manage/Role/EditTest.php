<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        $this->seed(PermissionSeeder::class);

        $response = $this->getRequest(
            route('manage.roles.edit', ['id' => Role::create(['name' => 'test'])->id]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'role',
            'permissions' => [
                '*' => [
                    'value',
                    'label',
                ]
            ],
            'permissionIds'
        ]);
        $this->assertNotEmpty($response->json('permissions'));
    }
}
