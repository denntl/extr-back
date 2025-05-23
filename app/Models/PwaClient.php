<?php

namespace App\Models;

use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithUuidExternalId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $application_id
 * @property string $external_id
 *
 * @property-read Application $application
 * @property-read PwaClientClick[] $pwaClientClicks
 */
class PwaClient extends Model
{
    use WithUuidExternalId;
    use WithExternalId;
    use HasFactory;

    protected string $external_id_field = 'external_id';

    public function getUpdatedAtColumn(): null
    {
        return null;
    }

    protected $fillable = [
        'application_id',
        'external_id',
    ];

    public static function getCountOfUnique(int $applicationId): int
    {
        return PwaClient::query()
            ->where('application_id', $applicationId)
            ->count();
    }

    public static function getCountOfUniqueByDate(int $applicationId, Carbon $date): int
    {
        $dateString = $date->toDateString();

        return DB::table(DB::raw("(SELECT pwa_clients.id
                               FROM pwa_clients
                               JOIN pwa_client_clicks ON pwa_clients.id = pwa_client_clicks.pwa_client_id
                               WHERE application_id = ?
                               AND DATE(pwa_clients.created_at) = ?
                               GROUP BY pwa_clients.id) as t"))
            ->setBindings([$applicationId, $dateString])
            ->count();
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function pwaClientClicks(): HasMany
    {
        return $this->hasMany(PwaClientClick::class, 'pwa_client_id', 'id');
    }
}
