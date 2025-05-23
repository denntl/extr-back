<?php

namespace Tests\Feature\Admin\Client\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class CloneTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $comment = ApplicationComment::factory()->create();
        /** @var Application $application */
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);
        $response = $this->postRequest(route('client.application.comment.clone'), [
            'public_id' => $application->public_id,
            'id' => $comment->id,
        ], $token);

        $response->assertStatus(200);

        $this->assertCount(2, ApplicationComment::all()->where('text', $comment->text));
    }

    public function testHasNoAccessToApplication()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $comment = ApplicationComment::factory()->create();
        /** @var Application $application */
        $application = Application::factory()->create();
        $response = $this->postRequest(route('client.application.comment.clone'), [
            'public_id' => $application->public_id,
            'id' => $comment->id,
        ], $token);

        $response->assertStatus(404);
    }
}
