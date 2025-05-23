<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->decimal('balance_before', 8, 2)->nullable();
            $table->decimal('balance_after', 8, 2)->nullable();
            $table->smallInteger('balance_type');
            $table->smallInteger('type');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_transactions');
    }
};
