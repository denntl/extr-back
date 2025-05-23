<?php

namespace Tests\Feature\Admin\Common\Auth;

use App\Enums\Invite\ActionName;
use App\Models\Company;
use App\Models\Invite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GetInviteTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
        ]);

        $invite = Invite::factory()->create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'expire_at' => Carbon::now()->addDay(),
            'action' => ActionName::Registration->value,
        ]);

        $response = $this->getJson(route('common.auth.invite', ['key' => $invite->key]));

        $response->assertStatus(200);
        $response->assertJson([
            'key' => $invite->key,
            'expire' => Carbon::parse($invite->expire_at)->toDateTimeString(),
            'companyName' => $company->name,
        ]);
    }

    public function testNotFound()
    {
        $response = $this->getJson(route('common.auth.invite', ['key' => 'undefined']));

        $response->assertStatus(404);
    }
}
