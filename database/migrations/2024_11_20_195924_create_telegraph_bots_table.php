<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('telegraph_bots', function (Blueprint $table) {
            $table->id();
            $table->string('token')->nullable()->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->boolean('is_active')->default(false);
            $table->string('public_id');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegraph_bots');
    }
};
