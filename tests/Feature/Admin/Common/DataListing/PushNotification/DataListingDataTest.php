<?php

namespace Tests\Feature\Admin\Common\DataListing\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\Application;
use App\Models\PushNotification;
use App\Models\PushTemplate;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        $template = PushNotification::factory()->create();

        [$token]  = $this->getUserToken(PermissionName::ClientSinglePushNotificationRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-single-notifications']),
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
        [$token, $user]  = $this->getUserToken(PermissionName::ClientSinglePushNotificationRead);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'created_by_id' => $user->id,
            'status' => 2,
            'company_id' => $user->company_id,
        ]);
        $templates = PushNotification::factory()
            ->create([
                'type' => Type::Single->value,
                'application_id' => $application->id,
                'created_by' => $user->id,
            ]);
        /**
         * @var $target PushNotification
         */
        $target = $templates->first();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-single-notifications']),
            ['query' => $target->name],
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
            'type' => $target->type,
            'application' => $target->application->subdomain . '.' . $target->application->domain->domain,
            'template' => $target->pushTemplate->name,
            'name' => $target->name,
            'geo' => $target->geo,
            'events' => $target->events,
            'status' => $target->status,
            'send_time' => $target->date . ' ' . $target->time,
            'creator' => $target->createdBy->name,
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token, $user]  = $this->getUserToken(PermissionName::ClientSinglePushNotificationRead);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'created_by_id' => $user->id,
            'status' => 2,
            'company_id' => $user->company_id,
        ]);
        $templates = PushNotification::factory()->count(5)->create([
            'type' => Type::Single->value,
            'application_id' => $application->id,
            'created_by' => $user->id,
        ]);
        /**
         * @var $target PushNotification
         */
        $target = $templates->first();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'push-single-notifications']),
            [
                'filters' => [
                    'name' => ['name' => 'name', 'operator' => FilterOperator::Contains, 'value' => $target->name],
                    'creator' => ['name' => 'createdBy', 'operator' => FilterOperator::In, 'value' => [$target->created_by]],
                    'application' => ['name' => 'application', 'operator' => FilterOperator::Equal, 'value' => $target->application->full_domain],
                    'events' => ['name' => 'events', 'operator' => FilterOperator::In, 'value' => $target->events],
                    'geo' => ['name' => 'geo', 'operator' => FilterOperator::In, 'value' => $target->geo],
                    'template' => ['name' => 'template', 'operator' => FilterOperator::In, 'value' => [$target->push_template_id]],
                    'send_time' => ['name' => 'send_time', 'operator' => FilterOperator::Equal, 'value' => $target->date . ' ' . $target->time],
                    'status' => ['name' => 'status', 'operator' => FilterOperator::In, 'value' => [$target->status]],
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

        $this->assertDatabaseHas('push_notifications', ['name' => $target->name]);

        $this->assertEquals([[
            'id' => $target->id,
            'type' => $target->type,
            'application' => $target->application->subdomain . '.' . $target->application->domain->domain,
            'template' => $target->pushTemplate->name,
            'name' => $target->name,
            'geo' => $target->geo,
            'events' => $target->events,
            'status' => $target->status,
            'send_time' => $target->date . ' ' . $target->time,
            'creator' => $target->createdBy->name,
        ]], $response->json('data'));
    }
}
