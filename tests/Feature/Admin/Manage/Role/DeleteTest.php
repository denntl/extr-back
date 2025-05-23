<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use JetBrains\PhpStorm\NoReturn;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleDelete);

        $this->seed(PermissionSeeder::class);

        $roleId = Role::create(['name' => 'test'])->id;
        $response = $this->getRequest(
            route('manage.roles.delete', ['id' => $roleId]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'canBeDeleted',
            'message'
        ]);
        $this->assertTrue($response->json('canBeDeleted'));
    }
    public function testIsNotDeleteable()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageRoleDelete);

        $this->seed(PermissionSeeder::class);

        $role = Role::create(['name' => 'test']);
        $user->syncRoles($role->id);
        $response = $this->getRequest(
            route('manage.roles.delete', ['id' => $role->id]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'canBeDeleted',
            'message',
            'users' => [
                '*' => [
                    'roleIds',
                    'company_name',
                    'company_id',
                    'username',
                ]
            ],
            'roles'
        ]);
        $this->assertFalse($response->json('canBeDeleted'));
    }
}
