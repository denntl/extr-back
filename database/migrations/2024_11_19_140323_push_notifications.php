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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('application_id');
            $table->bigInteger('push_template_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->smallInteger('type');
            $table->smallInteger('status');
            $table->boolean('is_active')->default(false);
            $table->string('name');
            $table->date('date')->nullable();
            $table->time('time');
            $table->string('geo');
            $table->string('events');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('icon')->nullable();
            $table->text('image')->nullable();
            $table->text('link')->nullable();

            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->foreign('push_template_id')->references('id')->on('push_templates')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
