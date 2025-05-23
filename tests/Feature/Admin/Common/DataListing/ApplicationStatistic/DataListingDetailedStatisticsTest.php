<?php

namespace Tests\Feature\Admin\Common\DataListing\ApplicationStatistic;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use App\Models\PwaClientEvent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDetailedStatisticsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        $appStats = ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'clicks' => 10,
            'push_subscriptions' => 111,
            'opens' => 5823,
            'installs' => 2000,
            'unique_clicks' => 1536
        ]);
        $pwaClient = PWAClient::factory()->create([
            'application_id' => $app->id,
        ]);
        $pwaClientClick = PWAClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);
        $pwaClientEvent = PWAClientEvent::factory()->count(5)->create([
            'created_at' => '2019-01-01 22:00:00',
            'pwa_client_click_id' => $pwaClientClick->id,
        ]);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'detailed-statistics']),
            [
                'params' => ['id' => $appStats->id]
            ],
            $token,
            false
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

    public function testNoParams()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        $appStats = ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'clicks' => 10,
            'push_subscriptions' => 111,
            'opens' => 5823,
            'installs' => 2000,
            'unique_clicks' => 1536
        ]);
        $pwaClient = PWAClient::factory()->create([
            'application_id' => $app->id,
        ]);
        $pwaClientClick = PWAClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);
        $pwaClientEvent = PWAClientEvent::factory()->count(5)->create([
            'created_at' => '2019-01-01 22:00:00',
            'pwa_client_click_id' => $pwaClientClick->id,
        ]);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'detailed-statistics']),
            [],
            $token,
            false
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

        $this->assertEquals(0, count($response->json('data')));
    }
    public function testInvalidParams()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $app = Application::factory()->create([
            'company_id' => $company->id,
            'owner_id' => $user->id,
        ]);
        $appStats = ApplicationStatistic::factory()->create([
            'date' => '2019-01-01',
            'application_id' => $app->id,
            'clicks' => 10,
            'push_subscriptions' => 111,
            'opens' => 5823,
            'installs' => 2000,
            'unique_clicks' => 1536
        ]);
        $pwaClient = PWAClient::factory()->create([
            'application_id' => $app->id,
        ]);
        $pwaClientClick = PWAClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);
        $pwaClientEvent = PWAClientEvent::factory()->count(5)->create([
            'created_at' => '2019-01-01 22:00:00',
            'pwa_client_click_id' => $pwaClientClick->id,
        ]);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'detailed-statistics']),
            [
                'params' => ['id' => 'app_id_3'],
            ],
            $token,
            false
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors'
        ]);

        $response->assertJsonValidationErrors([
            'params.id',
        ]);
    }
}
