<?php

namespace Tests\Feature\Admin\Common\DataListing\PushTemplate;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\PushTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        $template = PushTemplate::factory()->create();

        [$token]  = $this->getUserToken(PermissionName::ManagePushTemplateRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-templates']),
            [],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);
    }

    public function testSuccessQuery()
    {
        [$token]  = $this->getUserToken(PermissionName::ManagePushTemplateRead);

        $templates = PushTemplate::factory()->count(5)->create();
        /**
         * @var $target PushTemplate
         */
        $target = $templates->first();
        $target->name = 'test string';
        $target->save();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-templates']),
            ['query' => 'est strin'],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);

        $this->assertEquals(1, count($response->json('data')));

        $this->assertEquals([[
            'id' => $target->id,
            'name' => $target->name,
            'geo' => $target->geo,
            'events' => $target->events,
            'owner' => $target->created_by,
            'is_active' => $target->is_active,
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token]  = $this->getUserToken(PermissionName::ManagePushTemplateRead);

        $templates = PushTemplate::factory()->count(5)->create();
        /**
         * @var $target PushTemplate
         */
        $target = $templates->first();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-templates']),
            [
                'filters' => [
                    'name' => ['name' => 'name', 'operator' => 'contains', 'value' => $target->name],
                    'geo' => ['name' => 'owner', 'operator' => 'in', 'value' => [$target->created_by]],
                ]
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);

        $this->assertEquals(1, count($response->json('data')));

        $this->assertEquals([[
            'id' => $target->id,
            'name' => $target->name,
            'geo' => $target->geo,
            'events' => $target->events,
            'owner' => $target->created_by,
            'is_active' => $target->is_active,
        ]], $response->json('data'));
    }
}
