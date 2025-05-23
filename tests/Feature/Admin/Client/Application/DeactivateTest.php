<?php

namespace Tests\Feature\Admin\Client\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use Tests\TestCase;

class DeactivateTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->postRequest(route('client.application.deactivate', ['id' => $application->public_id]), [], $token);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully deactivated']);

        $this->assertDatabaseHas('applications', [
            'public_id' => $application->public_id,
            'status' => Status::NotActive,
        ]);
    }

    public function testHasNoAccessToApplication()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $application = Application::factory()->create();

        $response = $this->postRequest(route('client.application.clone', ['id' => $application->public_id]), [], $token);

        $response->assertStatus(404);
    }
}
