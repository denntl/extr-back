<?php

namespace Tests\Feature\Admin\Manage\PushTemplate;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushTemplate\Event;
use App\Models\PushTemplate;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePushTemplateUpdate);

        /**
         * @var $template PushTemplate
         */
        $template = PushTemplate::factory()->create()->first();

        $response = $this->postRequest(
            route('manage.push-template.update', ['id' => $template->id]),
            [
                'name' => 'test',
                'geo' => [Geo::UA->value],
                'events' => [Event::INSTALL->value],
                'is_active' => true,
                'title' => 'test_title',
                'content' => 'test_content',
                'icon' => 'test/icon',
                'image'  => 'test/image',
                'link' => 'test/link',
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('push_templates', [
            'id' => $template->id,
            'name' => 'test',
            'geo' => '["' . Geo::UA->value . '"]',
            'events' => '["' . Event::INSTALL->value . '"]',
            'is_active' => true,
            'title' => 'test_title',
            'content' => 'test_content',
            'icon' => 'test/icon',
            'image'  => 'test/image',
            'link' => 'test/link',
        ]);
    }
}
