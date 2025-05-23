<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    public function testSuccessDestroyRole()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleDelete);

        $this->seed(PermissionSeeder::class);

        $role = Role::create(['name' => 'test']);
        $user = User::first();

        $response = $this->deleteRequest(
            route('manage.roles.destroy', ['id' => $role->id]),
            ['users' => [
                [
                    'userId' => $user->id,
                    'roleIds' => $user->roles->pluck('id')->toArray(),
                ]
            ]],
            $token,
        );

        $response->assertStatus(200);
        $this->assertTrue($response->json('isDeleted'));
    }
    public function testRoleHasUsers()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleDelete);
        $role = Role::create(['name' => 'test']);
        $user = User::first()->syncRoles($role->id);

        $response = $this->deleteRequest(
            route('manage.roles.destroy', ['id' => $role->id]),
            ['users' => [
                [
                    'userId' => $user->id,
                    'roleIds' => $user->roles->pluck('id')->toArray(),
                ]
            ]],
            $token,
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'roleId'
            ],
        ]);
    }
    public function testRemovePredefinedRoles()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleDelete);

        $role = Role::create([
            'name' => 'Test Role',
            'guard_name' => 'web',
            'is_predefined' => true,
        ]);

        $response = $this->deleteRequest(
            route('manage.roles.destroy', ['id' => $role->id]),
            [],
            $token,
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'roleId'
            ],
        ]);

        $response->assertJson([
            'message' => 'Вы не можете удалить предопределенную роль'
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'is_predefined' => true,
            'name' => 'Test Role',
        ]);
    }
}
