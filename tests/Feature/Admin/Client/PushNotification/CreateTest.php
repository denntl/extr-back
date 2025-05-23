<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushTemplate\Event;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientPushNotificationCreate);

        $response = $this->getRequest(
            route('client.push-notification.create'),
            $token
        );

        $response->assertStatus(200);
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Event::cases()), $response->json('events'));
    }
}
