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
        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('is_top_application', 'display_top_bar');
            $table->renameColumn('is_top_applications', 'display_app_bar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('display_top_bar', 'is_top_application');
            $table->renameColumn('display_app_bar', 'is_top_applications');
        });
    }
};
