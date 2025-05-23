<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_templates_single_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onesignal_template_id')
                ->constrained('onesignal_templates');
            $table->timestamp('scheduled_at');
            $table->timestamp('handled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_templates_single_settings');
    }
};
