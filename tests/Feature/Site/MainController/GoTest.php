<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\PwaEvents\Event;
use App\Models\Application;
use App\Models\ApplicationStatistic;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class GoTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
        ]);

        $pwaClient = PwaClient::factory()->create([
            'application_id' => $application->id,
        ]);

        /** @var PwaClientClick $pwaClientClick */
        $pwaClientClick = PwaClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);

        ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'clicks' => 10,
            'unique_clicks' => 5,
            'registrations' => 2,
            'installs' => 0,
            'dep_to_ins' => 0,
            'deposits' => 0,
            'opens' => 0,
            'first_opens' => 0,
            'repeated_opens' => 0,
        ]);

        $url = route('go') . "?com=$application->uuid&externalId=$pwaClientClick->external_id&onesignal=ok";
        $response = $this->get($url);
        $response->assertStatus(302)
            ->assertRedirect($pwaClientClick->link);

        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Open->value,
        ]);

        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'clicks' => 10,
            'unique_clicks' => 5,
            'registrations' => 2,
            'installs' => 0,
            'dep_to_ins' => 0,
            'deposits' => 0,
            'opens' => 1,
            'first_opens' => 1,
            'repeated_opens' => 0,
        ]);
    }
}
