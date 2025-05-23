<?php

namespace Tests\Feature\Admin\Common\DataListing\Domains;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::Domains->value]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'domain', 'state' => 'asc'], $response->json('sorting'));
    }
}
