<?php

namespace Tests\Feature\Admin\Manage\PushTemplate;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushTemplate\Event;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePushTemplateCreate);

        $response = $this->getRequest(
            route('manage.push-template.create'),
            $token
        );

        $response->assertStatus(200);
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Event::cases()), $response->json('events'));
    }
}
