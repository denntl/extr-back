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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('key', 32)->unique()->comment('Unique key of invite');
            $table->tinyInteger('provider')->comment('Id of provider: 1 - Telegram');
            $table->dateTime('expire_at')->comment('Date time string when invite expires');
            $table->unsignedInteger('invited_by')->comment('ID of user that created invite for user');
            $table->unsignedInteger('company_id')->comment('Id of company in what user was invited');
            $table->unsignedInteger('team_id')->nullable()->comment('Id of team in what user was invited. If null there is no relation to team');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invites');
    }
};
