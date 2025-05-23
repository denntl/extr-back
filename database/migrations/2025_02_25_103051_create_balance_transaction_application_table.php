<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('balance_transaction_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balance_transaction_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_transaction_applications');
    }
};
