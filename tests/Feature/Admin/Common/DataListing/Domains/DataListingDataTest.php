<?php

namespace Tests\Feature\Admin\Common\DataListing\Domains;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\Company;
use App\Models\Domain;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainRead);

        Domain::factory()->count(13)->create();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::Domains->value]),
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
        [$token] = $this->getUserToken(PermissionName::ManageDomainRead);

        $domains = Domain::factory()->count(13)->create();
        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'domains'],
            ),
            [
                'page' => 1,
                'sorting' => [
                    'column' => 'domain',
                    'state' => 'asc'
                ],
                'filters' => [],
                'query' => $domains->toArray()[0]['domain']
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
    }

    public function testSuccessFilter()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainRead);

        Domain::factory()->count(13)->create();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'domains'],
            ),
            [
                'page' => 1,
                'sorting' => [
                    'column' => 'domain',
                    'state' => 'asc'
                ],
                'filters' => [
                    "status" => [
                        "name" => "status",
                        "operator" => "in",
                        "value" => [1]
                    ]
                ],
                'query' => ''
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

        $this->assertEquals(Domain::where('status', 1)->get()->count(), $response->json('total'));
    }
}
