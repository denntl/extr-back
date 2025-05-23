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
    public function up(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->timestamp('last_onesignal_created_at')->nullable()->after('is_delayed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropColumn('last_onesignal_created_at');
        });
    }
};
