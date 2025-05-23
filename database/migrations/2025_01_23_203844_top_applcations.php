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
            $table->boolean('is_top_applications')->default(false);
        });
        Schema::create('top_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('parent_application_id');
            $table->bigInteger('child_application_id');

            $table->foreign('parent_application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->foreign('child_application_id')->references('id')->on('applications')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('is_top_applications');
        });
        Schema::dropIfExists('top_applications');
    }
};
