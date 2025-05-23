<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushTemplate\Event;
use App\Models\PushNotification;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientPushNotificationUpdate);
        /**
         * @var $notification PushNotification
         */
        $notification = PushNotification::factory()->create()->first();

        $response = $this->getRequest(
            route('client.push-notification.edit', ['id' => $notification->id]),
            $token
        );

        $response->assertStatus(200);
        $this->assertEquals($notification->toArray(), $response->json('values'));
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Event::cases()), $response->json('events'));
    }
}
