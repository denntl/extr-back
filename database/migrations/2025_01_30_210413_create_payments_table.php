<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('processor_id');
            $table->foreignId('balance_transaction_id')->constrained('balance_transactions')->onDelete('cascade');
            $table->string('external_id', 255)->nullable();
            $table->tinyInteger('status');
            $table->string('comment', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
