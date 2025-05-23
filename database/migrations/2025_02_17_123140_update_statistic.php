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
            $table->integer('opens')->default(0);
            $table->integer('first_opens')->default(0);
            $table->integer('repeated_opens')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_statistics', function (Blueprint $table) {
            $table->dropColumn('opens');
            $table->dropColumn('first_opens');
            $table->dropColumn('repeated_opens');
        });
    }
};
