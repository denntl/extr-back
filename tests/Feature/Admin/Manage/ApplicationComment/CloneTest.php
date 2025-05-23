<?php

namespace Tests\Feature\Admin\Manage\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class CloneTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);

        $comment = ApplicationComment::factory()->create();
        /** @var Application $application */
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);
        $response = $this->postRequest(route('manage.application.comment.clone'), [
            'application_id' => $application->id,
            'id' => $comment->id,
        ], $token);

        $response->assertStatus(200);

        $this->assertCount(2, ApplicationComment::all()->where('text', $comment->text));
    }
}
