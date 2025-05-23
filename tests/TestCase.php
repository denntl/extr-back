<?php

namespace Tests;

use App\Enums\Authorization\PermissionName;
use App\Enums\User\Status;
use App\Http\Middleware\PermissionMiddleware;
use App\Models\Company;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function getUserToken(?PermissionName $permission = null, $tariffTypeId = 1): array
    {
        $company = Company::factory()->create([
            'tariff_id' => Tariff::factory()->create(['type_id' => $tariffTypeId]),
        ]);

        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
            'status' => Status::Active,
        ]);
        $company->update(['owner_id' => $user->id]);

        if ($permission) {
            $this->assignPermissionToUser($user, $permission);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [$token, $user, $company];
    }

    protected function getRequest(string $url, string $token, ?bool $skipPermissions = true): TestResponse
    {
        if ($skipPermissions) {
            $this->withoutMiddleware(PermissionMiddleware::class);
        }

        return $this->getJson($url, [
            'authorization' => "Bearer $token",
        ]);
    }

    protected function postRequest(string $url, array $params, string $token, ?bool $skipPermissions = true): TestResponse
    {
        if ($skipPermissions) {
            $this->withoutMiddleware(PermissionMiddleware::class);
        }

        return $this->postJson($url, $params, [
            'authorization' => "Bearer $token",
        ]);
    }

    protected function putRequest(string $url, array $params, string $token, ?bool $skipPermissions = true): TestResponse
    {
        if ($skipPermissions) {
            $this->withoutMiddleware(PermissionMiddleware::class);
        }

        return $this->putJson($url, $params, [
            'authorization' => "Bearer $token",
        ]);
    }

    protected function deleteRequest(string $url, array $params, string $token, ?bool $skipPermissions = true): TestResponse
    {
        if ($skipPermissions) {
            $this->withoutMiddleware(PermissionMiddleware::class);
        }

        return $this->deleteJson($url, $params, [
            'authorization' => "Bearer $token",
        ]);
    }

    protected function assignPermissionToUser(User $user, PermissionName $permission): void
    {
        Permission::findOrCreate($permission->value, 'web');
        $user->givePermissionTo($permission->value);
    }
}
