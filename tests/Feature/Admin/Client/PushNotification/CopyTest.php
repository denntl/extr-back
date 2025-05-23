<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Authorization\PermissionName;
use App\Models\PushNotification;
use Tests\TestCase;

class CopyTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientPushNotificationClone);

        /**
         * @var $notification PushNotification
         */
        $notification = PushNotification::factory()->create()->first();

        $response = $this->postRequest(
            route('client.push-notification.copy', ['id' => $notification->id]),
            [],
            $token
        );
        $response->assertStatus(200);
        $this->assertDatabaseHas('push_notifications', [
            'id' => $response->json('id'),
            'push_template_id' => $notification->push_template_id,
            'application_id' => $notification->application_id,
            'type' => $notification->type,
            'date' => $notification->date,
            'time' => $notification->time,
            'geo' => json_encode($notification->geo),
            'events' => json_encode($notification->events),
            'is_active' => false,
            'title' => $notification->title,
            'content' => $notification->content,
            'icon' => $notification->icon,
            'image'  => $notification->image,
            'link' => $notification->link,
        ]);
    }
}
