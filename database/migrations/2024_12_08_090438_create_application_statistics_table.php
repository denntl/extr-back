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
        Schema::create('application_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('application_id');
            $table->integer('clicks')->default(0);
            $table->integer('push_subscriptions')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->integer('installs')->default(0);
            $table->integer('deposits')->default(0);
            $table->integer('registrations')->default(0);
            $table->float('ins_to_uc')->default(0);
            $table->float('reg_to_ins')->default(0);
            $table->float('dep_to_ins')->default(0);
            $table->float('dep_to_reg')->default(0);
            $table->timestamps();

            $table->unique(['date', 'application_id']);
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_statistics');
    }
};
