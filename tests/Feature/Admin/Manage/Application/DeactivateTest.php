<?php

namespace Tests\Feature\Admin\Manage\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use Tests\TestCase;

class DeactivateTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->postRequest(route('manage.application.deactivate', ['id' => $application->id]), [], $token);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully deactivated']);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => Status::NotActive,
        ]);
    }
}
