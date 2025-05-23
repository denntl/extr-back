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
        Schema::table('pwa_clients', function (Blueprint $table) {
            $table->dropColumn('ip');
            $table->dropColumn('useragent');
            $table->dropColumn('sub_1');
            $table->dropColumn('sub_2');
            $table->dropColumn('sub_3');
            $table->dropColumn('sub_4');
            $table->dropColumn('sub_5');
            $table->dropColumn('sub_6');
            $table->dropColumn('sub_7');
            $table->dropColumn('sub_8');
            $table->dropColumn('fb_p');
            $table->dropColumn('fb_c');
            $table->dropColumn('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwa_clients', function (Blueprint $table) {
            $table->string('ip')->nullable();
            $table->text('useragent')->nullable();
            $table->string('sub_1')->nullable();
            $table->string('sub_2')->nullable();
            $table->string('sub_3')->nullable();
            $table->string('sub_4')->nullable();
            $table->string('sub_5')->nullable();
            $table->string('sub_6')->nullable();
            $table->string('sub_7')->nullable();
            $table->string('sub_8')->nullable();
            $table->string('fb_p')->nullable();
            $table->string('fb_c')->nullable();
            $table->string('link')->nullable();
        });
    }
};
