<?php

namespace Tests\Feature\Admin\Client\Application;

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
        Permission::create(['name' => PermissionName::ClientApplicationOwnerUpdate->value]);
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
            'platform_type' => $this->faker->randomElement(PlatformType::cases())->value,
            'landing_type' => $this->faker->randomElement(LandingType::cases())->value,
            'category' => $this->faker->randomElement(Category::cases())->value,
            'white_type' => $this->faker->randomElement(WhiteType::cases())->value,
            'status' => Status::NotActive,
        ];
        $params = array_merge([
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
            'public_id' => $application->public_id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('client.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['public_id'] = $application->public_id;

        $this->assertDatabaseHas('applications', $expectedParams);
    }

    public function testUpdateWithOwnerSuccess()
    {
        Permission::create(['name' => PermissionName::ClientApplicationOwnerUpdate->value]);
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);
        $domain = Domain::factory()->create();
        $user->permissions()->attach(Permission::where('name', PermissionName::ClientApplicationOwnerUpdate->value)->first());

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
        ];
        $params = array_merge([
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
            'public_id' => $application->public_id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('client.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['public_id'] = $application->public_id;

        $this->assertDatabaseHas('applications', $expectedParams);
    }

    public function testUpdateWithOwnerFail()
    {
        Permission::create(['name' => PermissionName::ClientApplicationOwnerUpdate->value]);
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);
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
            'status' => Status::NotActive,
            'owner_id' => $user->id,
        ];
        $params = array_merge([
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
            'public_id' => $application->public_id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('client.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['public_id'] = $application->public_id;
        $expectedParams['owner_id'] = $oldOwner->id;

        $this->assertDatabaseHas('applications', $expectedParams);
    }

    public function testSuccessContent()
    {
        Permission::create(['name' => PermissionName::ClientApplicationOwnerUpdate->value]);

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
            'downloads_count' => '2M+',
            'rating' => 4.6,
            'icon' => 'http://test.icon',
            'images' => [],
            'status' => Status::Active,
        ];
        $params = array_merge([
            'domain_id' => $domain->id,
            'public_id' => $application->public_id,
            'name' => 'Test name',
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
        ], $newParams);

        $this->assertDatabaseHas('applications', [
            'public_id' => $application->public_id,
            'company_id' => $user->company_id,
        ]);
        $response = $this->postRequest(route('client.application.update', ['stage' => 'platform']), $params, $token);

        $response->assertStatus(200);

        $expectedParams = $newParams;
        $expectedParams['public_id'] = $application->public_id;
        unset($expectedParams['images']);

        $this->assertDatabaseHas('applications', $expectedParams);
    }
}
