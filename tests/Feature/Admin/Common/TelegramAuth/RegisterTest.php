<?php

namespace Tests\Feature\Admin\Common\TelegramAuth;

use App\Models\Invite;
use App\Models\User;
use App\Services\Common\Telegram\TelegramService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testRegisterFail()
    {
        $response = $this->postJson(route('common.auth.telegram.register', ['key' => 'invalid_key']), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['errors' => [
            'common' => [
                'Регистрация неуспешна, проверьте Telegram аккаунт или обратитесь к вашему личному менеджеру',
            ]
        ]]);
    }

    public function testRegisterFailedHash()
    {
        Invite::factory()->create([
            'key' => 'valid_key',
        ]);

        $response = $this->postJson(route('common.auth.telegram.register', ['key' => 'valid_key']), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['errors' => [
            'common' => [
                'Регистрация неуспешна, проверьте Telegram аккаунт или обратитесь к вашему личному менеджеру',
            ]
        ]]);
    }

    public function testRegisterSuccess()
    {
        $role = Role::create([
            'name' => 'Байер',
            'guard_name' => 'web'
        ]);

        $telegramServiceMock = $this->createMock(TelegramService::class);
        $telegramServiceMock->method('isValidAuthRequest')->willReturn(true);
        $this->app->instance(TelegramService::class, $telegramServiceMock);

        $invite = Invite::factory()->create([
            'key' => 'valid_key',
        ]);

        $response = $this->postJson(route('common.auth.telegram.register', ['key' => 'valid_key']), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'access',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Test Test',
            'username' => 'test',
            'telegram_id' => 1234567890,
            'company_id' => $invite->company_id,
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Байер',
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => User::where('username', 'test')->first()->id,
        ]);
    }
}
