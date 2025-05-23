<?php

namespace App\Models;

use App\Enums\PwaEvents\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property bool $is_handled
 * @property int $pwa_client_click_id
 * @property string $date
 * @property string $time
 * @property string $event
 * @property string|null $details
 * @property bool $is_first
 *
 * @property-read PwaClientClick $pwaClientClick
 */
class PwaClientEvent extends Model
{
    use HasFactory;

    public function getUpdatedAtColumn(): null
    {
        return null;
    }

    protected $fillable = [
        'is_handled',
        'pwa_client_click_id',
        'event',
        'details',
        'is_first',
        'full_domain',
        'geo',
        'platform'
    ];

    protected $casts = [
        'is_handled' => 'bool',
        'details' => 'array',
        'geo' => 'array',
    ];

    public function getApplicationId(): int
    {
        return self::query()
            ->select(['pwa_clients.application_id'])
            ->join('pwa_client_clicks', 'pwa_client_clicks.id', '=', 'pwa_client_events.pwa_client_click_id')
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->where('pwa_client_events.id', $this->id)
            ->firstOrFail()
            ->getAttribute('application_id');
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return self::query()
            ->select(['applications.company_id'])
            ->join('pwa_client_clicks', 'pwa_client_clicks.id', '=', 'pwa_client_events.pwa_client_click_id')
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->join('applications', 'applications.id', '=', 'pwa_clients.application_id')
            ->where('pwa_client_events.id', $this->id)
            ->firstOrFail()
            ->getAttribute('company_id');
    }

    public static function getCountByDate(int $applicationId, Event $event, Carbon $date, bool $unique = false): int
    {
        $dateString = $date->toDateString();

        $query = self::query()
            ->join('pwa_client_clicks', 'pwa_client_clicks.id', '=', 'pwa_client_events.pwa_client_click_id')
            ->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id')
            ->where('pwa_clients.application_id', $applicationId)
            ->where('pwa_client_events.event', $event->value)
            ->whereRaw("date(pwa_client_events.created_at) = '$dateString'");

        if ($unique) {
            $query->where('is_first', $unique);
        }

        return $query->count();
    }

    public function scopeClientClick(Builder $builder): void
    {
        $builder->join('pwa_client_clicks', 'pwa_client_clicks.id', '=', 'pwa_client_events.pwa_client_click_id');
    }

    public function scopeClient(Builder $builder): void
    {
        if (!$builder->hasNamedScope('clientClick')) {
            $this->scopeClientClick($builder);
        }
        $builder->join('pwa_clients', 'pwa_clients.id', '=', 'pwa_client_clicks.pwa_client_id');
    }

    public function scopeApplication(Builder $builder): void
    {
        if (!$builder->hasNamedScope('client')) {
            $this->scopeClient($builder);
        }
        $builder->join('applications', 'pwa_clients.application_id', '=', 'applications.id');
    }

    public function pwaClientClick(): BelongsTo
    {
        return $this->belongsTo(PwaClientClick::class, 'pwa_client_click_id', 'id');
    }
}
