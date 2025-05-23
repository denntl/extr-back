<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\Application;
use App\Models\OneSignalNotification;
use App\Models\PushNotification;
use App\Models\PushTemplate;
use Carbon\Carbon;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientPushNotificationUpdate);
        /** @var PushTemplate $template */
        $template  = PushTemplate::factory()->create()->first();
        /** @var Application $application */
        $application = Application::factory()->create()->first();

        $tomorrowDate = Carbon::now()->addDay()->format('Y-m-d');

        /**
         * @var PushNotification $notification
         */
        $notification = PushNotification::factory()->create([
            'is_active' => true,
            'date' => $tomorrowDate,
            'time' => '04:05',
            'type' => Type::Single->value,
        ])->first();

        OneSignalNotification::factory()->create([
            'push_notifications_id' => $notification->id,
        ]);

        $response = $this->postRequest(
            route('client.push-notification.update', ['id' => $notification->id]),
            [
                'push_template_id' => $template->id,
                'application_id' => $application->id,
                'type' => Type::Single->value,
                'date' => $tomorrowDate,
                'time' => '05:05:00',
                'name' => 'test',
                'geo' => [Geo::UA->value],
                'events' => [Event::INSTALL->value],
                'is_active' => true,
                'title' => 'test_title',
                'content' => 'test_content',
                'icon' => 'test/icon',
                'image'  => 'test/image',
                'link' => 'https://google.com',
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('push_notifications', [
            'id' => $notification->id,
            'push_template_id' => $template->id,
            'application_id' => $application->id,
            'type' => Type::Single->value,
            'date' => $tomorrowDate,
            'time' => '05:05:00',
            'name' => 'test',
            'geo' => "[\"UA\"]",
            'events' => "[\"install\"]",
            'is_active' => true,
            'title' => 'test_title',
            'content' => 'test_content',
            'icon' => 'test/icon',
            'image'  => 'test/image',
            'link' => 'https://google.com',
        ]);
    }
}
