<?php

namespace Tests\Feature\Admin\Common\DataListing\ManageApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageApplicationSave);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::ManageApplicationComment->value]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'created_at', 'state' => 'desc'], $response->json('sorting'));
    }
}
