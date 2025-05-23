<?php

namespace Tests\Feature\Admin\Common\DataListing\ApplicationStatistic;

use App\Enums\Authorization\PermissionName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDetailedStatisticsSettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => 'detailed-statistics']),
            $token,
            false
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'date', 'state' => 'desc'], $response->json('sorting'));
    }

    public function testHasNoAccess()
    {
        [$token] = $this->getUserToken();

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => 'detailed-statistics']),
            $token,
            false
        );

        $response->assertStatus(403);
    }
}
