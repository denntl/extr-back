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
        Schema::create('application_geo_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('application_id');
            $table->string('geo', 3);
            $table->string('language', 3);

            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->unique(['application_id', 'geo']);
        });

        $applications = DB::table('applications')->select('id', 'geo', 'language')->get();
        foreach ($applications as $application) {
            if (!$application->geo) {
                continue;
            }
            $geos = json_decode($application->geo, true);
            foreach ($geos as $geo) {
                DB::table('application_geo_languages')->insert([
                    'application_id' => $application->id,
                    'geo' => $geo,
                    'language' => $application->language,
                ]);
            }
        }
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('geo');
            $table->dropColumn('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('geo')->nullable()->default('[]');
            $table->string('language', 5)->nullable();
        });
        $results = [];
        $geosLang = DB::table('application_geo_languages')->select('application_id', 'geo', 'language')->get();
        foreach ($geosLang as $value) {
            if (!isset($results[$value->application_id])) {
                $results[$value->application_id] = ['geo' => [], 'language' => null];
            }
            $results[$value->application_id]['geo'][] = $value->geo;
            $results[$value->application_id]['language'] = $value->language;
        }

        foreach ($results as $id => $result) {
            DB::table('applications')->where('id', $id)->update([
                'geo' => json_encode($result['geo']),
                'language' => $result['language']
            ]);
        }

        Schema::dropIfExists('application_geo_languages');
    }
};
