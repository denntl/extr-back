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
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->dropColumn('pwa_client_id');
            $table->boolean('is_handled')->default(false);
            $table->unsignedBigInteger('pwa_client_click_id');

            $table->foreign('pwa_client_click_id')
                ->references('id')
                ->on('pwa_client_clicks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->dropForeign(['pwa_client_click_id']);
            $table->dropColumn(['is_handled', 'pwa_client_click_id']);
            $table->unsignedBigInteger('pwa_client_id');
        });
    }
};
