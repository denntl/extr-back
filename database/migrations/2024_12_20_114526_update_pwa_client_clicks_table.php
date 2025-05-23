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
        Schema::table('pwa_client_clicks', function (Blueprint $table) {
            $table->string('external_id')->unique();
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
            $table->string('pixel_id')->nullable();
            $table->string('pixel_key')->nullable();
            $table->string('fb_click_id')->nullable();
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwa_client_clicks', function (Blueprint $table) {
            $table->dropColumn([
                'external_id',
                'ip',
                'useragent',
                'sub_1',
                'sub_2',
                'sub_3',
                'sub_4',
                'sub_5',
                'sub_6',
                'sub_7',
                'sub_8',
                'fb_p',
                'fb_c',
                'pixel_id',
                'pixel_key',
                'fb_click_id',
                'link',
            ]);
        });
    }
};
