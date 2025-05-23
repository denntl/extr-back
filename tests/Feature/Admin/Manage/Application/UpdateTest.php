<?php

namespace Tests\Feature\Admin\Manage\Application;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\LandingType;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\Application\WhiteType;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\Domain;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use WithFaker;

    public function testSuccessPlatform()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);
        $domain = Domain::factory()->create();

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'domain_id' => $domain->id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'app_name' => 'test',
            'status' => Status::Active,
        ]);

        $newParams = [
            'platform_type' => $this->faker->randomElement(PlatformType::cases())->value,
            'landing_type' => $this->faker->randomElement(LandingType::cases())->value,
            'category' => $this->faker->randomElement(Category::cases())->value,
            'white_type' => $this->faker->randomElement(WhiteType::cases())->value,
            'status' => Status::NotActive,
            'onesignal_auth_key' => 'one-signal-api-key',
        ];
        $params = array_merge([
            'id' => $application->id,
            'domain_id' => $domain->id,
            'public_id' => $application->public_id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'app_name' => 'test',
        ], $newParams);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'company_id' => $application->company_id,
        ]);
        $response = $this->postRequest(route('manage.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['id'] = $application->id;

        $this->assertDatabaseHas('applications', $expectedParams);
    }

    public function testUpdateWithOwnerSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);
        $domain = Domain::factory()->create();

        $oldOwner = User::factory()->create([
            'company_id' => $user->company_id,
        ]);
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'domain_id' => $domain->id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'app_name' => 'test',
            'owner_id' => $oldOwner->id,
            'status' => Status::Active,
        ]);

        $newParams = [
            'platform_type' => $this->faker->randomElement(PlatformType::cases())->value,
            'landing_type' => $this->faker->randomElement(LandingType::cases())->value,
            'category' => $this->faker->randomElement(Category::cases())->value,
            'white_type' => $this->faker->randomElement(WhiteType::cases())->value,
            'owner_id' => $user->id,
            'status' => Status::NotActive,
            'onesignal_auth_key' => 'one-signal-api-key',
        ];
        $params = array_merge([
            'domain_id' => $domain->id,
            'public_id' => $application->public_id,
            'id' => $application->id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'app_name' => 'test',
        ], $newParams);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('manage.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['id'] = $application->id;

        $this->assertDatabaseHas('applications', $expectedParams);
    }

    public function testSuccessContent()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);
        $domain = Domain::factory()->create();

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'domain_id' => $domain->id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'app_name' => 'test',
            'status' => Status::Active,
        ]);

        $newParams = [
            'app_name' => 'test name',
            'developer_name' => 'test developer',
            'description' => 'test description',
            'downloads_count' => '11M',
            'rating' => 3.78,
            'icon' => 'http://test.icon',
            'images' => [],
            'status' => Status::Active,
            'onesignal_auth_key' => 'one-signal-api-key',
        ];
        $params = array_merge([
            'id' => $application->id,
            'domain_id' => $domain->id,
            'public_id' => $application->public_id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
        ], $newParams);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('manage.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['id'] = $application->id;
        unset($expectedParams['images']);

        $this->assertDatabaseHas('applications', $expectedParams);
    }
}
