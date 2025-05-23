<?php

namespace Tests\Feature\Admin\Manage\PushTemplate;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushTemplate\Event;
use App\Models\PushTemplate;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePushTemplateUpdate);
        /**
         * @var $template PushTemplate
         */
        $template = PushTemplate::factory()->create()->first();

        $response = $this->getRequest(
            route('manage.push-template.edit', ['id' => $template->id]),
            $token
        );

        $response->assertStatus(200);
        $this->assertEquals($template->toArray(), $response->json('values'));
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Event::cases()), $response->json('events'));
    }
}
