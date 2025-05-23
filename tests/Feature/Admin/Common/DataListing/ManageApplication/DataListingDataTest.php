<?php

namespace Tests\Feature\Admin\Common\DataListing\ManageApplication;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageApplicationRead);

        Application::factory()->count(2)->create();
        Application::factory()->create([
            'deleted_at' => now()->toDateTimeString()
        ]);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::ManageApplication->value]),
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

        $this->assertCount(3, $response->json('data'));
        $this->assertDatabaseCount('applications', 3);
    }

    public function testSuccessQuery()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationRead);

        $date = now()->toDateTimeString();
        $applications = Application::factory()->count(5)->create([
            'deleted_at' => $date
        ]);

        /** @var Application $application */
        $application = $applications->first();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::ManageApplication->value],
            ),
            ['query' => $application->name],
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
            'id' => $application->id,
            'company_id' => $application->company_id,
            'name' => $application->name,
            'full_domain' => $application->full_domain,
            'owner' => $application->owner->name,
            'geos' => null,
            'status' => $application->status,
            'app_uuid' => $application->uuid,
            'deleted' => date('Y-m-d H:i:s', strtotime($date))
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationRead);

        $applications = Application::factory()->count(5)->create();

        /** @var Application $application */
        $application = $applications->first();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::ManageApplication->value],
            ),
            ['filters' => [
                'owner' => ['name' => 'owner', 'operator' => 'in', 'value' => [$application->owner_id]],
                'name' => ['name' => 'name', 'operator' => 'contains', 'value' => substr($application->name, 1, 5)],
                'company_id' => ['name' => 'company_id', 'operator' => 'in', 'value' => [$application->company_id]],
                'full_domain' => ['name' => 'full_domain', 'operator' => 'contains', 'value' => substr($application->full_domain, 1, 5)],
                'status' => ['name' => 'status', 'operator' => 'in', 'value' => [$application->status]],
                'app_uuid' => ['name' => 'app_uuid', 'operator' => 'eq', 'value' => $application->uuid],

            ]],
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
            'id' => $application->id,
            'company_id' => $application->company_id,
            'name' => $application->name,
            'full_domain' => $application->full_domain,
            'owner' => $application->owner->name,
            'geos' => null,
            'status' => $application->status,
            'app_uuid' => $application->uuid,
            'deleted' => null
        ]], $response->json('data'));
    }
}
