<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $pwa_client_id
 * @property Carbon $created_at
 * @property string $external_id
 * @property string $ip
 * @property string $useragent
 * @property string $sub_1
 * @property string $sub_2
 * @property string $sub_3
 * @property string $sub_4
 * @property string $sub_5
 * @property string $sub_6
 * @property string $sub_7
 * @property string $sub_8
 * @property string $fb_p
 * @property string $fb_c
 * @property string $pixel_id
 * @property string $pixel_key
 * @property string $link
 * @property string $request_url
 * @property string $country
 *
 * @property-read PwaClient $pwaClient
 */
class PwaClientClick extends Model
{
    use HasFactory;

    public function getUpdatedAtColumn(): null
    {
        return null;
    }

    protected $fillable = [
        'pwa_client_id',
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
        'fb_click_id',
        'pixel_id',
        'pixel_key',
        'link',
        'request_url',
        'country',
    ];

    public static function getCount(int $applicationId): int
    {
        return self::query()
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->where('pwa_clients.application_id', $applicationId)
            ->count();
    }

    public static function getCountByDate(int $applicationId, Carbon $date): int
    {
        $dateString = $date->toDateString();

        return self::query()
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->where('pwa_clients.application_id', $applicationId)
            ->whereRaw("date(pwa_client_clicks.created_at) = '$dateString'")
            ->count();
    }

    public function getApplicationId(): int
    {
        return self::query()
            ->select(['pwa_clients.application_id'])
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->where('pwa_client_clicks.id', $this->id)
            ->firstOrFail()
            ->getAttribute('application_id');
    }

    public function pwaClient(): BelongsTo
    {
        return $this->belongsTo(PwaClient::class, 'pwa_client_id', 'id');
    }

    public function event(): HasMany
    {
        return $this->hasMany(PwaClientEvent::class, 'pwa_client_click_id', 'id');
    }
}
