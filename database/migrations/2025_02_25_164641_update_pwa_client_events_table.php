<?php

use App\Models\PwaClientEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->string('full_domain')->nullable();
            $table->string('geo')->nullable();
            $table->string('platform')->nullable();
        });

        PwaClientEvent::query()
            ->select('pwa_client_events.id', 'applications.full_domain', 'applications.geo', 'applications.platform_type as platform')
            ->join('pwa_client_clicks', 'pwa_client_events.pwa_client_click_id', '=', 'pwa_client_clicks.id')
            ->join('pwa_clients', 'pwa_client_clicks.pwa_client_id', '=', 'pwa_clients.id')
            ->join('applications', 'pwa_clients.application_id', '=', 'applications.id')
            ->chunk(50, function ($statistics) {
                foreach ($statistics as $statistic) {
                    $pwaClientEvent = PwaClientEvent::find($statistic->id);
                    $platform = match ($statistic->platform) {
                        1 => 'androidOS',
                        2 => 'iOS',
                        default => 'unknown',
                    };
                    $pwaClientEvent->update([
                        'full_domain' => $statistic->full_domain,
                        'geo' => $statistic->geo,
                        'platform' => $platform
                    ]);
                }
            });

        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->string('full_domain')->change();
            $table->string('geo')->change();
            $table->string('platform')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pwa_client_events', function (Blueprint $table) {
            $table->dropColumn('full_domain');
            $table->dropColumn('geo');
            $table->dropColumn('platform');
        });
    }
};
