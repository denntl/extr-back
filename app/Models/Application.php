<?php

namespace App\Models;

use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithSoftDeletesNumberExternalId;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateUpdateApplicationRequestDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Application
 *
 * @property int $id
 * @property int $status
 * @property int $public_id
 * @property int $company_id
 * @property int $created_by_id
 * @property int $owner_id
 * @property string $uuid
 * @property string $name
 * @property int $domain_id
 * @property string|null $subdomain
 * @property string|null $full_domain
 * @property string|null $pixel_id
 * @property string|null $pixel_key
 * @property string $link
 * @property int $platform_type
 * @property int $landing_type
 * @property int $white_type
 * @property string $language
 * @property int $category
 * @property string $app_name
 * @property string $developer_name
 * @property string|null $icon
 * @property string|null $description
 * @property int $downloads_count
 * @property float $rating
 * @property string|null $onesignal_id
 * @property string|null $onesignal_name
 * @property string|null $onesignal_auth_key
 * @property boolean|null $displayTopBar
 * @property boolean|null $displayAppBar
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Domain $domain
 * @property-read User $owner
 * @property File[]|Collection $files
 * @property-read ApplicationComment[] $applicationComments
 * @property Application[]|Collection $topApplications
 * @property ApplicationGeoLanguage[] $applicationGeoLanguages
 */
class Application extends Model
{
    use HasFactory;
    use WithExternalId;
    use WithSoftDeletesNumberExternalId;
    use SoftDeletes;

    // For preview
    public ?array $images = null;

    public ?string $language = null;

    protected $fillable = [
        'status',
        'public_id',
        'company_id',
        'created_by_id',
        'owner_id',
        'uuid',
        'name',
        'full_domain',
        'domain_id',
        'subdomain',
        'pixel_id',
        'pixel_key',
        'link',
        'platform_type',
        'landing_type',
        'white_type',
        'category',
        'app_name',
        'developer_name',
        'icon',
        'description',
        'downloads_count',
        'rating',
        'onesignal_id',
        'onesignal_name',
        'onesignal_auth_key',
        'display_top_bar',
        'display_app_bar',
    ];

    protected $casts = [
        'geo' => 'array',
    ];

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeStatistic(Builder $builder): void
    {
        $rawQuery = '(select application_id, sum(clicks) as click_sum, sum(unique_clicks) as unique_click_sum, sum(installs) as install_sum,'
            . ' sum(registrations) as registration_sum, sum(deposits) as deposit_sum from application_statistics group by application_id) app_stats';
        $builder->leftJoin(
            DB::raw($rawQuery),
            'applications.id',
            '=',
            'app_stats.application_id'
        );
    }

    public function scopeGeo(Builder $builder)
    {
        $rawQuery = '(select application_geo_languages.application_id, STRING_AGG(application_geo_languages.geo, \', \') as geos'
            . ' from application_geo_languages group by application_geo_languages.application_id) app_geos';
        $builder->leftJoin(
            DB::raw($rawQuery),
            'applications.id',
            '=',
            'app_geos.application_id'
        );
        $builder->leftJoin(
            'application_geo_languages',
            'applications.id',
            '=',
            'application_geo_languages.application_id'
        );
    }

    /**
     * Get all files associated with the application.
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'application_file', 'application_id', 'file_id')->orderByPivot('position');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'id');
    }

    public function applicationComments()
    {
        return $this->hasMany(ApplicationComment::class, 'application_id', 'id');
    }

    public function scopeOwner(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'applications.owner_id');
    }

    public function toOneSignalCreateApplicationDTO(): CreateUpdateApplicationRequestDTO
    {
        return new CreateUpdateApplicationRequestDTO($this->subdomain, $this->domain->domain);
    }

    public function pwaClients(): HasMany
    {
        return $this->hasMany(PwaClient::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function topApplications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'top_applications', 'parent_application_id', 'child_application_id');
    }

    public function getTopApplicationIdsAttribute()
    {
        return $this->topApplications->pluck('public_id')->toArray();
    }

    public function applicationGeoLanguages()
    {
        return $this->hasMany(ApplicationGeoLanguage::class, 'application_id', 'id');
    }

    public function getApplicationGeoLanguagesAttribute()
    {
        return $this->applicationGeoLanguages()->get(['geo', 'language'])->toArray();
    }

    public function getLanguage(string $geo): ?string
    {
        $lang = $this->applicationGeoLanguages()->where('geo', $geo)->first();
        return $lang ? $lang->language : null;
    }

    public function onesignalTemplates(): BelongsToMany
    {
        return $this->belongsToMany(OnesignalTemplate::class, 'onesignal_templates_applications', 'application_id', 'onesignal_template_id');
    }
}
