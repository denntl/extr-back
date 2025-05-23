<?php

use App\Enums\Application\Geo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('tier_countries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tier_id');
                $table->string('country', 3);
                $table->timestamps();

                $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('cascade');
            });

            foreach (Geo::cases() as $geo) {
                DB::table('tier_countries')->insert([
                    'tier_id' => 4,
                    'country' => $geo->value,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tier_countries', function (Blueprint $table) {
            $table->dropForeign(['tier_id']);
        });

        Schema::dropIfExists('tier_countries');
    }
};
