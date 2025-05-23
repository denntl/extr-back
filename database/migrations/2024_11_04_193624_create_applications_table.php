<?php

use App\Enums\Application\Status;
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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->default(Status::Active->value);
            $table->unsignedInteger('public_id');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('owner_id')->constrained('users');
            $table->uuid();
            $table->string('name');
            $table->foreignId('domain_id')->constrained('domains');
            $table->string('subdomain')->nullable();
            $table->string('pixel_id')->nullable();
            $table->string('pixel_key')->nullable();
            $table->text('link');
            $table->text('geo')->nullable();
            $table->unsignedTinyInteger('platform_type');
            $table->unsignedTinyInteger('landing_type');
            $table->unsignedTinyInteger('white_type');
            $table->string('language', 5)->nullable();
            $table->unsignedTinyInteger('category');
            $table->string('app_name');
            $table->string('developer_name')->nullable();
            $table->text('icon')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('downloads_count')->nullable();
            $table->float('rating')->nullable();
            $table->string('onesignal_id')->nullable();
            $table->string('onesignal_name')->nullable();
            $table->string('onesignal_auth_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
