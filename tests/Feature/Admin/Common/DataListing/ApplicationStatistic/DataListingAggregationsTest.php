<?php

namespace Tests\Feature\Admin\Common\DataListing\ApplicationStatistic;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Tests\TestCase;

class DataListingAggregationsTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'clicks' => 10,
            'push_subscriptions' => 111,
            'opens' => 5823,
            'installs' => 2000,
            'unique_clicks' => 1536
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-02',
            'application_id' => $app->id,
            'clicks' => 15,
            'push_subscriptions' => 123,
            'opens' => 6789,
            'installs' => 4000,
            'unique_clicks' => 3000
        ]);

        $response = $this->postRequest(
            route('common.listing.aggregations', ['entity' => EntityName::ApplicationStatistics->value]),
            [],
            $token,
            false
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'name',
                'label'
            ],
        ]);

        $clicks_total = array_filter($response->json(), fn($el) => $el['name'] === 'clicks');
        $this->assertEquals(10 + 15, array_pop($clicks_total)['label']);

        $push_subscriptions_total = array_filter($response->json(), fn($el) => $el['name'] === 'push_subscriptions');
        $this->assertEquals(111 + 123, array_pop($push_subscriptions_total)['label']);

        $ins_to_uc = array_filter($response->json(), fn($el) => $el['name'] === 'ins_to_uc');
        $ins_to_uc_total = array_pop($ins_to_uc);
        $this->assertEquals(
            round(100 * (2000 + 4000) / (1536 + 3000), 2) . ' %',
            $ins_to_uc_total['label']
        );
    }
    public function testSuccessZeroUniqClicks()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'installs' => 2000,
            'unique_clicks' => 0
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-02',
            'application_id' => $app->id,
            'installs' => 4000,
            'unique_clicks' => 0
        ]);

        $response = $this->postRequest(
            route('common.listing.aggregations', ['entity' => EntityName::ApplicationStatistics->value]),
            [],
            $token,
            false
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'name',
                'label'
            ],
        ]);

        $ins_to_uc = array_filter($response->json(), fn($el) => $el['name'] === 'ins_to_uc');
        $ins_to_uc_total = array_pop($ins_to_uc);
        $this->assertEquals(0, $ins_to_uc_total['label']);
    }
    public function testSuccessFiltered()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'installs' => 2000,
            'unique_clicks' => 0
        ]);
        ApplicationStatistic::factory()->create([
            'date' => '2019-01-02',
            'application_id' => $app->id,
            'installs' => 4000,
            'unique_clicks' => 3000
        ]);

        $response = $this->postRequest(
            route('common.listing.aggregations', ['entity' => EntityName::ApplicationStatistics->value]),
            [
                'filters' => [
                    'date' => [
                        'name' => 'date',
                        'operator' => FilterOperator::GreaterThanOrEqual->value,
                        'value' => '2019-01-02'
                    ]
                ]
            ],
            $token,
            false
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'name',
                'label'
            ],
        ]);

        $ins_to_uc = array_filter($response->json(), fn($el) => $el['name'] === 'ins_to_uc');
        $ins_to_uc_total = array_pop($ins_to_uc);
        $this->assertEquals(
            round(100 * (4000) / (3000), 2) . ' %',
            $ins_to_uc_total['label']
        );
    }
}
