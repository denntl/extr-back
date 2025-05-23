<?php

namespace Tests\Feature\Admin\Client\Statistic;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use Carbon\Carbon;
use Tests\TestCase;

class TotalTest extends TestCase
{
    public function testHasNoPermission(): void
    {
        [$token] = $this->getUserToken();
        $response = $this->getRequest(route('client.statistic.get-daily'), $token, false);
        $response->assertStatus(403);
    }

    public function testSuccess(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        /** @var ApplicationStatistic $stat */
        $stat = ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => Carbon::now(),
        ]);

        $response = $this->getRequest(route('client.statistic.get-daily'), $token);

        $response->assertOk();
        $response->assertJson([
            'uniqueClicks' => $stat->unique_clicks,
            'installs' => $stat->installs,
            'registrations' => $stat->registrations,
            'deposits' => $stat->deposits,
        ]);
    }
}
