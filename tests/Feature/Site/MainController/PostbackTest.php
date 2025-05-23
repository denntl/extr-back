<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\PwaEvents\Event;
use App\Models\Application;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostbackTest extends TestCase
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
            'uuid' => Str::uuid(),
        ]);
        $pwaClient = PwaClient::factory()->create([
            'application_id' => $application->id,
        ]);

        /** @var PwaClientClick $pwaClientClick */
        $pwaClientClick = PwaClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);

        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Registration->value,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Registration->value,
        ]);
        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'registrations' => 1,
            'dep_to_reg' => 0,
        ]);

        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Deposit->value,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Deposit->value,
        ]);
        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'deposits' => 1,
            'registrations' => 1,
            'dep_to_reg' => 1,
        ]);

        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Deposit->value,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Deposit->value,
        ]);
        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'deposits' => 1,
            'registrations' => 1,
            'dep_to_reg' => 1,
        ]);
    }

    public function testNoDuplicateRegDep()
    {
        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
            'uuid' => Str::uuid(),
        ]);
        $pwaClient = PwaClient::factory()->create([
            'application_id' => $application->id,
        ]);
        /** @var PwaClientClick $pwaClientClick */
        $pwaClientClick = PwaClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
        ]);

        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Registration->value,
        ]);
        $response->assertStatus(200);
        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Registration->value,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Registration->value,
        ]);
        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'registrations' => 1,
        ]);

        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Deposit->value,
        ]);
        $response->assertStatus(200);
        $response = $this->post(route('postback'), [
            'external_id' => $pwaClientClick->external_id,
            'status' => Event::Deposit->value,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => Event::Deposit->value,
        ]);
        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $application->id,
            'date' => now()->toDateString(),
            'deposits' => 1,
        ]);
    }
}
