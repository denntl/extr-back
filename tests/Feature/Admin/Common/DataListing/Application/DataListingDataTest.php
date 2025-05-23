<?php

namespace Tests\Feature\Admin\Common\DataListing\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\Application;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationRead);

        Application::factory()->count(2)->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);
        Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'status' => Status::NotActive,
            'deleted_at' => now()->toDateTimeString()
        ]);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::Application->value]),
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

        $this->assertCount(2, $response->json('data'));
        $this->assertDatabaseCount('applications', 3);
    }

    public function testSuccessQuery()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageCompanyRead);

        $companies = Company::factory()->count(5)->create();

        /** @var Company $companyWillBeSelected */
        $companyWillBeSelected = $companies->first();
        $companyWillBeSelected->name = 'q1w2e3r4t5y6u7';
        $companyWillBeSelected->owner_id = $user->id;
        $companyWillBeSelected->save();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::Company->value],
            ),
            ['query' => '2e3r4t5y6'],
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
            'id' => $companyWillBeSelected->id,
            'created_at' => $companyWillBeSelected->created_at->toDateString(),
            'updated_at' => $companyWillBeSelected->updated_at->toDateString(),
            'name' => $companyWillBeSelected->name,
            'owner' => $user->name,
            'user_count' => null,
            'team_count' => null,
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageCompanyRead);

        $companies = Company::factory()->count(5)->create();

        /** @var Company $companyWillBeSelected */
        $companyWillBeSelected = $companies->first();
        $companyWillBeSelected->owner_id = $user->id;
        $companyWillBeSelected->save();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::Company->value],
            ),
            ['filters' => [
                'owner' => ['name' => 'owner', 'operator' => 'in', 'value' => [$user->id]],
                'name' => ['name' => 'name', 'operator' => 'contains', 'value' => substr($companyWillBeSelected->name, 1, 5)],
                'created_at' => ['name' => 'created_at', 'operator' => 'lte', 'value' => $companyWillBeSelected->created_at->addDay()->toDateString()],
                'updated_at' => ['name' => 'updated_at', 'operator' => 'between', 'value' => [
                    $companyWillBeSelected->updated_at->subDay()->toDateString(),
                    $companyWillBeSelected->updated_at->addDay()->toDateString(),
                ]],
                'id' => ['name' => 'id', 'operator' => 'eq', 'value' => $companyWillBeSelected->id],

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
            'id' => $companyWillBeSelected->id,
            'created_at' => $companyWillBeSelected->created_at->toDateString(),
            'updated_at' => $companyWillBeSelected->updated_at->toDateString(),
            'name' => $companyWillBeSelected->name,
            'owner' => $user->name,
            'user_count' => null,
            'team_count' => null,
        ]], $response->json('data'));
    }
}
