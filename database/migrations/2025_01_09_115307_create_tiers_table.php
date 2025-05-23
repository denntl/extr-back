<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('tariff_id');
                $table->decimal('price', 3, 2);
                $table->timestamps();

                $table->foreign('tariff_id')->references('id')->on('tariffs')->onDelete('cascade');
            });

            DB::table('tiers')->insert([
                ['price' => 0.1, 'tariff_id' => 1, 'name' => 'Тир 1'],
                ['price' => 0.2, 'tariff_id' => 1, 'name' => 'Тир 2'],
                ['price' => 0.3, 'tariff_id' => 1, 'name' => 'Тир 3'],
                ['price' => 0.4, 'tariff_id' => 1, 'name' => 'Тир 4'],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
