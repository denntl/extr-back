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
        Schema::create('application_comments', function (Blueprint $table) {
            $table->id();
            $table->string('author_name', 255);
            $table->text('text');
            $table->integer('stars');
            $table->string('lang', 5);
            $table->string('icon', 255);
            $table->foreignId('created_by')->constrained('users');
            $table->text('answer')->nullable();
            $table->date('date')->nullable();
            $table->integer('application_id')->nullable();
            $table->integer('origin_id')->nullable();
            $table->integer('likes')->default(0);
            $table->string('answer_author', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_comments');
    }
};
