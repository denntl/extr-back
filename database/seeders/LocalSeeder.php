<?php

namespace Database\Seeders;

use App\Enums\Authorization\RoleName;
use App\Enums\User\Status;
use App\Models\ApplicationComment;
use App\Models\ApplicationStatistic;
use App\Models\Company;
use App\Models\User;
use App\Services\Common\Company\CompanyService;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LocalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //includes domains, applications, users, companies
        ApplicationStatistic::factory()->count(23)->create();
        ApplicationComment::factory()->count(25)->create();

        $this->addTestUser();
    }

    private function addTestUser()
    {
        $company = Company::create([
            'name' => 'Test Company',
        ]);
        $user = User::create([
            'name' => 'testing',
            'email' => 'test@test.com',
            'password' => bcrypt('12345678'),
            'username' => 'testing',
            'status' => Status::NewReg->value,
            'company_id' => $company->id,
            'is_employee' => false,
        ]);
        app(CompanyService::class)->setCompanyOwner($company, $user);

        User::find($user->id)->assignRole(RoleName::Admin->value);
    }
}
