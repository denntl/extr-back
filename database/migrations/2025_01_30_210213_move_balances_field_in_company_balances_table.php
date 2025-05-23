<?php

use App\Models\Company;
use App\Models\CompanyBalance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->decimal('balance', 8, 2)->default(0.00);
            $table->decimal('balance_bonus', 8, 2)->default(0.00);
            $table->unique('company_id');
            $table->timestamps();
        });
        $companies = Company::all();
        foreach ($companies as $company) {
            CompanyBalance::create(
                ['company_id' => $company->id],
            );
        }
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['balance', 'balance_bonus']);
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('balance', 8, 2)->default(0);
            $table->decimal('balance_bonus', 8, 2)->default(0);
        });
        Schema::dropIfExists('company_balances');
    }
};
