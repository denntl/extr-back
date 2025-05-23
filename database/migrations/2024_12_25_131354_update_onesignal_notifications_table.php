<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('onesignal_notifications', function (Blueprint $table) {
            $table->timestamp('last_webhook_accepted_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onesignal_notifications', function (Blueprint $table) {
            $table->dropColumn('last_webhook_accepted_at');
        });
    }
};
