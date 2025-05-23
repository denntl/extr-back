<?php

namespace Tests\Feature\Admin\Client\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationDelete);

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'status' => Status::Active
        ]);

        $response = $this->postRequest(route('manage.application.delete', ['id' => $application->id]), [], $token);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully deleted']);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => Status::NotActive,
        ]);
        $this->assertCount(1, Application::onlyTrashed()->get());
    }
}
