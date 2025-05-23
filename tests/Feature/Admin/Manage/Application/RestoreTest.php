<?php

namespace Tests\Feature\Admin\Manage\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationDelete);

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'status' => Status::NotActive,
            'deleted_at' => now()->toDateTimeString()
        ]);
        $this->assertCount(1, Application::onlyTrashed()->get());

        $response = $this->postRequest(route('manage.application.restore', ['id' => $application->id]), [], $token);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully restored']);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => Status::NotActive,
            'deleted_at' => null,
        ]);
        $this->assertCount(0, Application::onlyTrashed()->get());
    }
}
