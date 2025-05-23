<?php

namespace Tests\Feature\Admin\Common\TelegramAuth;

use App\Enums\User\Status;
use App\Models\Company;
use App\Models\Invite;
use App\Models\User;
use App\Services\Common\Telegram\TelegramService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testFail()
    {
        $response = $this->postJson(route('common.auth.telegram.login'), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(422);
    }

    public function testFailedHash()
    {
        User::factory()->create([
            'telegram_id' => 1234567890,
        ]);

        $response = $this->postJson(route('common.auth.telegram.login'), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(422);
    }

    public function testSuccess()
    {
        $telegramServiceMock = $this->createMock(TelegramService::class);
        $telegramServiceMock->method('isValidAuthRequest')->willReturn(true);
        $this->app->instance(TelegramService::class, $telegramServiceMock);

        User::factory()->create([
            'telegram_id' => 1234567890,
            'status' => Status::Active
        ]);

        $response = $this->postJson(route('common.auth.telegram.login'), [
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
    }

    public function testUserBlocked()
    {
        $telegramServiceMock = $this->createMock(TelegramService::class);
        $telegramServiceMock->method('isValidAuthRequest')->willReturn(true);
        $this->app->instance(TelegramService::class, $telegramServiceMock);

        User::factory()->create([
            'telegram_id' => 1234567890,
            'status' => Status::Deleted,
        ]);

        $response = $this->postJson(route('common.auth.telegram.login'), [
            'auth_date' => 1234567890,
            'first_name' => 'Test',
            'id' => 1234567890,
            'last_name' => 'Test',
            'username' => 'test',
            'hash' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'common' => []
            ]
        ]);
    }
}
