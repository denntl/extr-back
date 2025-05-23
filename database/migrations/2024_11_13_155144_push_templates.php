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
        Schema::create('push_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('geo', 255);
            $table->string('events', 255);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('title', 255);
            $table->text('content');
            $table->text('icon');
            $table->text('image');
            $table->text('link')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_templates');
    }
};
