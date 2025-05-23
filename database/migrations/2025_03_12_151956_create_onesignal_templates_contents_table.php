<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_templates_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onesignal_template_id')->constrained('onesignal_templates');
            $table->foreignId('geo_id')->constrained('geos');
            $table->string('title');
            $table->text('text');
            $table->text('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_templates_contents');
    }
};
