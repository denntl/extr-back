<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('balance_transactions', function (Blueprint $table) {
            DB::table('balance_transactions')->whereNull('user_id')->update(['user_id' => 0]);
            $table->unsignedBigInteger('user_id')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->change();
        });
        Schema::table('balance_transactions', function (Blueprint $table) {
            DB::table('balance_transactions')->where('user_id', 0)->update(['user_id' => null]);
        });
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
