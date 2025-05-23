<?php

namespace Tests\Feature\Admin\Client\TelegramBot;

use App\Enums\Authorization\PermissionName;
use App\Models\TelegraphBot;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientNotificationTelegramBotUpdate);

        $telegramBot = TelegraphBot::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $response = $this->getRequest(route('client.notifications.telegram-bot.edit'), $token, false);

        $response->assertStatus(200);
        $response->assertJson([
            'telegramBot' => [
                'is_active' => $telegramBot->is_active,
                'token' => $telegramBot->token,
                'name' => $telegramBot->name,
                'public_id' => $telegramBot->public_id,
            ]
        ]);
    }

    public function testSuccessCreate()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientNotificationTelegramBotUpdate);

        $response = $this->getRequest(route('client.notifications.telegram-bot.edit'), $token, false);

        $response->assertStatus(200);
        $this->assertDatabaseHas('telegraph_bots', [
            'company_id' => $user->company_id,
        ])->assertDatabaseCount('telegraph_bots', 1);
    }
}
