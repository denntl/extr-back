<?php

namespace Tests\Feature\Admin\Client\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\Application;
use App\Models\PushTemplate;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePushTemplateCreate);
        $template  = PushTemplate::factory()->create()->first();
        $application = Application::factory()->create()->first();

        $response = $this->postRequest(
            route('client.push-notification.store'),
            [
                'push_template_id' => $template->id,
                'application_id' => $application->id,
                'type' => Type::Single->value,
                'date' => '2024-12-12',
                'time' => '05:05',
                'name' => 'test',
                'geo' => [Geo::UA->value],
                'events' => [Event::INSTALL->value],
                'is_active' => false,
                'title' => 'test_title',
                'content' => 'test_content',
                'icon' => 'test/icon',
                'image'  => 'test/image',
                'link' => 'test/link',
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('push_notifications', [
            'push_template_id' => $template->id,
            'application_id' => $application->id,
            'type' => Type::Single->value,
            'date' => '2024-12-12',
            'time' => '05:05',
            'name' => 'test',
            'geo' => "[\"UA\"]",
            'events' => "[\"install\"]",
            'is_active' => false,
            'title' => 'test_title',
            'content' => 'test_content',
            'icon' => 'test/icon',
            'image'  => 'test/image',
            'link' => 'test/link',
        ]);
    }
}
