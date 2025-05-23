<?php

namespace Tests\Feature\Admin\Common\Auth;

use App\Enums\Authorization\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $role = Role::create([
            'name' => 'Руководитель компании',
            'guard_name' => 'web'
        ]);

        $response = $this->postJson(route('common.auth.register'), [
            'username' => 'User123',
            'companyName' => 'Company1',
            'telegramName' => 'TelegramUser',
            'email' => 'user@example.com',
            'password' => 'Password1!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'access'
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'User123',
            'email' => 'user@example.com'
        ])->exactly(1);

        $this->assertDatabaseHas('companies', [
            'name' => 'Company1',
        ])->exactly(1);

        $this->assertDatabaseHas('teams', [
            'name' => 'Команда Company1',
        ])->exactly(1);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Руководитель компании',
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => User::where('username', 'User123')->first()->id,
        ]);
    }
}
