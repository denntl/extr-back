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
        Schema::table('onesignal_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('push_notifications_id')->nullable()->change();

            $table->unsignedBigInteger('onesignal_template_id')->nullable()->after('push_notifications_id');
            $table->foreign('onesignal_template_id')->references('id')->on('onesignal_templates')->onDelete('cascade');

            $table->unsignedBigInteger('application_id')->nullable()->after('onesignal_template_id');
            $table->foreign('application_id')->references('id')->on('applications');

            $table->unsignedBigInteger('geo_id')->nullable()->after('application_id');
            $table->foreign('geo_id')->references('id')->on('geos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onesignal_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('push_notifications_id')->change();

            $table->dropColumn('onesignal_template_id');
            $table->dropColumn('application_id');
            $table->dropColumn('geo_id');
        });
    }
};
