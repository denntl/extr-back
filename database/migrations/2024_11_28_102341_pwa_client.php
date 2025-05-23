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
        Schema::create('pwa_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('application_id');
            $table->string('external_id')->unique();
            $table->timestamp('created_at');
            $table->string('ip')->nullable();
            $table->text('useragent')->nullable();
            $table->string('sub_1')->nullable();
            $table->string('sub_2')->nullable();
            $table->string('sub_3')->nullable();
            $table->string('sub_4')->nullable();
            $table->string('sub_5')->nullable();
            $table->string('sub_6')->nullable();
            $table->string('sub_7')->nullable();
            $table->string('sub_8')->nullable();
            $table->string('fb_p')->nullable();
            $table->string('fb_c')->nullable();
            $table->string('link')->nullable();

            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
        });

        Schema::create('pwa_client_clicks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pwa_client_id');
            $table->timestamp('created_at');

            $table->foreign('pwa_client_id')->references('id')->on('pwa_clients')->cascadeOnDelete();
        });

        Schema::create('pwa_client_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pwa_client_id');
            $table->timestamp('created_at');
            $table->string('event');
            $table->text('details')->nullable();

            $table->foreign('pwa_client_id')->references('id')->on('pwa_clients')->cascadeOnDelete();
            $table->unique(['event', 'pwa_client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pwa_client_events');
        Schema::dropIfExists('pwa_client_clicks');
        Schema::dropIfExists('pwa_clients');
    }
};
