<?php

namespace Tests\Feature\Admin\Common\DataListing\Company;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::Company->value]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'id', 'state' => 'desc'], $response->json('sorting'));
    }
}
