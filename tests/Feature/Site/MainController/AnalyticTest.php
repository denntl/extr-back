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

class AnalyticTest extends TestCase
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
        ]);

        $event = Event::Install->value;
        $url = route('analytic') . "?com=$application->uuid&t=$event&externalId=$pwaClientClick->external_id";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertJson([
            'redirect' => "http://localhost/go?com=$application->uuid&externalId=$pwaClientClick->external_id",
            'setting' => [
                'installing' => [
                    'ranges' => [
                        'step' => [
                            'min' => 10,
                            'max' => 15,
                        ],
                        'interval' => [
                            'min' => 1000,
                            'max' => 1500,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => $event,
        ]);

        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'clicks' => 10,
            'unique_clicks' => 5,
            'registrations' => 2,
            'installs' => 1,
            'first_installs' => 1,
            'repeated_installs' => 0,
            'deposits' => 0,
            'ins_to_uc' => '0.2',
            'reg_to_ins' => '2',
            'dep_to_ins' => '0',
            'dep_to_reg' => '0',
        ]);

        $response = $this->get($url);
        $response->assertStatus(200);

        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'clicks' => 10,
            'unique_clicks' => 5,
            'registrations' => 2,
            'installs' => 2,
            'first_installs' => 1,
            'repeated_installs' => 1,
            'deposits' => 0,
            'ins_to_uc' => '0.4',
            'reg_to_ins' => '1',
            'dep_to_ins' => '0',
            'dep_to_reg' => '0',
        ]);
    }

    public function testSuccessWithoutClick()
    {
        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
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
        ]);

        $event = Event::Install->value;
        $clickId = (string) Str::uuid();
        $url = route('analytic') . "?com=$application->uuid&t=$event&externalId=$clickId";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertJson([
            'redirect' => "http://localhost/go?com=$application->uuid&externalId=$clickId",
            'setting' => [
                'installing' => [
                    'ranges' => [
                        'step' => [
                            'min' => 10,
                            'max' => 15,
                        ],
                        'interval' => [
                            'min' => 1000,
                            'max' => 1500,
                        ],
                    ],
                ],
            ],
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
        ]);
    }
}
