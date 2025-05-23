<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->smallInteger('type');
            $table->boolean('is_active');
            $table->json('segments');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_templates');
    }
};
