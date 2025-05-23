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
        Schema::table('pwa_client_clicks', function (Blueprint $table) {
            $table->text('useragent')->change();
            $table->string('pixel_key', 555)->change();
            $table->string('link', 2222)->change();
            $table->string('ip', 25)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwa_client_clicks', function (Blueprint $table) {
            // Revert the changes if necessary
            $table->string('useragent')->change();
            $table->string('pixel_key')->change();
            $table->string('link')->change();
            $table->string('ip')->change();
        });
    }
};
