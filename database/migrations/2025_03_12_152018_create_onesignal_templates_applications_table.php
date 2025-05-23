<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_templates_applications', function (Blueprint $table) {
            $table->foreignId('onesignal_template_id')->constrained('onesignal_templates');
            $table->foreignId('application_id')->constrained('applications');
            $table->primary(['onesignal_template_id', 'application_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_templates_applications');
    }
};
