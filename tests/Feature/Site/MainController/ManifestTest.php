<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Models\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManifestTest extends TestCase
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


        $url = route('manifest') . "?app_uuid=$application->uuid";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'dir',
            'name',
            'scope',
            'display',
            'start_url',
            'short_name',
            'theme_color',
            'description',
            'orientation',
            'background_color',
            'prefer_related_applications',
            'icons',
            'url',
            'lang',
            'related_applications',
            'screenshots',
            'generated',
            'manifest_package',
            'scope_url',
            'intent_filters',
            'display_mode',
            'web_manifest_url',
            'version_code',
            'version_name',
            'bound_webapk',
        ]);
    }
}
