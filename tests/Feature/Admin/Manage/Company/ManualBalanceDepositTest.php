<?php

namespace Feature\Admin\Manage\Company;

use App\Enums\Authorization\PermissionName;
use App\Enums\Balance\Type;
use App\Models\Company;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManualBalanceDepositTest extends TestCase
{
    use WithFaker;

    public function testHasNoPermission()
    {
        [$token] = $this->getUserToken();
        $company = Company::factory()->create();

        $response = $this->postRequest(
            route('manage.company.manualBalanceDeposit', ['id' => $company->id]),
            [
                'amount' => $this->faker->randomFloat(2, 1, 1000),
                'comment' => $this->faker->realText(255),
                'balanceType' => Type::Balance->value
            ],
            $token,
            false
        );

        $response->assertStatus(403);
    }

    public function testInvalid()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyManualBalanceDeposit);
        $company = Company::factory()->create();

        $response = $this->postRequest(
            route('manage.company.manualBalanceDeposit', ['id' => $company->id]),
            [],
            $token
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'amount',
            'comment',
            'balanceType',
        ]);
    }

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyManualBalanceDeposit);
        $company = Company::factory()->create();

        $requestParams = [
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'comment' => $this->faker->realText(255),
            'balanceType' => Type::Balance->value,
        ];

        $response = $this->postRequest(
            route('manage.company.manualBalanceDeposit', ['id' => $company->id]),
            $requestParams,
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isUpdated' => true,
            'message' => 'Баланс компании успешно обновлен',
        ]);

        $this->assertDatabaseHas('company_balances', [
            'company_id' => $company->id,
            CompanyBalanceService::getColumnNameByTypeId($requestParams['balanceType']) => $requestParams['amount'],
        ]);
    }
}
