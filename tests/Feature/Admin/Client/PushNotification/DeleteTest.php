<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Status;
use App\Models\PushNotification;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientPushNotificationDelete);

        /**
         * @var $notification PushNotification
         */
        $notification = PushNotification::factory()->create([
            'status' => Status::Draft->value,
        ])->first();

        $response = $this->postRequest(
            route('client.push-notification.delete', ['id' => $notification->id]),
            [],
            $token
        );
        $response->assertStatus(200);
        $notification->refresh();
        $this->assertTrue($notification->trashed());
    }
}
