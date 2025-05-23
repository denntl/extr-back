<?php

use App\Services\Common\Tariff\Enums\Type;
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
            Schema::create('tariffs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('type_id');
                $table->timestamps();
            });

            DB::table('tariffs')->insert([
                'name' => 'Стандартный',
                'type_id' => Type::Install->value,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
};
