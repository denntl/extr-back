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
        Schema::create('notification_template_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_template_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('notification_template_id')->references('id')->on('notification_templates')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_template_roles');
    }
};
