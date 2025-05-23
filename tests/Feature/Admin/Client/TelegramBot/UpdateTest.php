<?php

namespace Tests\Feature\Admin\Client\TelegramBot;

use App\Enums\Authorization\PermissionName;
use App\Models\TelegraphBot;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientNotificationTelegramBotUpdate);

        TelegraphBot::factory()->create([
            'company_id' => $user->company_id,
            'is_active' => false,
        ]);

        $response = $this->putRequest(route('client.notifications.telegram-bot.update'), ['token' => 'test'], $token, false);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Successfully updated',
        ]);
    }

    public function testUpdateActive()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientNotificationTelegramBotUpdate);

        TelegraphBot::factory()->create([
            'company_id' => $user->company_id,
            'is_active' => true,
        ]);

        $response = $this->putRequest(route('client.notifications.telegram-bot.update'), ['token' => 'test'], $token, false);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Validation failed',
            ['is_active' => 'The telegram bot is active'],
        ]);
    }
}
