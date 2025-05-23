<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new fields
            $table->unsignedInteger('public_id')->comment('increment id inside one company. public value visible inside the company');
            $table->string('username', 15)->comment('telegram username');
            $table->tinyInteger('status')->comment('status of user: 0 - deleted, 1 - new registration, 2 - active');
            $table->unsignedInteger('company_id')->comment('ID of company user belongs to');
            $table->boolean('is_employee')->comment('true - user is our employee, false - user is not our employee');
            $table->string('name')->nullable()->change();

            // Add unique key
            $table->unique(['public_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove unique key
            $table->dropUnique(['public_id', 'company_id']);
            // Remove added fields
            $table->dropColumn(['public_id', 'username', 'status', 'company_id', 'is_employee']);
        });
    }
};
