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
        Schema::table('application_statistics', function (Blueprint $table) {
            $table->integer('first_installs')->default(0);
            $table->integer('repeated_installs')->default(0);
        });
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->boolean('is_first')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_statistics', function (Blueprint $table) {
            $table->dropColumn('first_installs');
            $table->dropColumn('repeated_installs');
        });
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->dropColumn('is_first');
        });
    }
};
