<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property boolean $is_active
 * @property array<int> $application_ids
 * @property array<int> $geos
 */
class OnesignalTemplate extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'segments',
        'created_by'
    ];

    protected $casts = [
        'segments' => 'array',
    ];

    public function isActive(): bool
    {
        return $this->is_active;
    }
    public function onesignalTemplateContents(): HasMany
    {
        return $this->hasMany(OnesignalTemplateContents::class, 'onesignal_template_id', 'id');
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'onesignal_templates_applications', 'onesignal_template_id', 'application_id');
    }

    public function singleSettings(): HasOne
    {
        return $this->hasOne(OnesignalTemplateSingleSettings::class, 'onesignal_template_id', 'id');
    }

    public function regularSettings(): HasOne
    {
        return $this->hasOne(OnesignalTemplateRegularSettings::class, 'onesignal_template_id', 'id');
    }

    public function scopeCreatedBy(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'onesignal_templates.created_by');
    }
    public function scopeApplications(Builder $builder): void
    {
        $builder->join(
            'onesignal_templates_applications',
            'onesignal_templates_applications.onesignal_template_id',
            '=',
            'onesignal_templates.id'
        );
        $builder->join(
            'applications',
            'onesignal_templates_applications.application_id',
            '=',
            'applications.id'
        );
    }

    public function scopeContents(Builder $builder): void
    {
        $builder->join('onesignal_templates_contents', 'onesignal_templates.id', '=', 'onesignal_templates_contents.onesignal_template_id');
    }
    public function scopeGeos(Builder $builder): void
    {
        if (!$this->isTableJoined($builder, 'onesignal_templates_contents')) {
            $this->scopeContents($builder);
        }
        $builder->join('geos', 'geos.id', '=', 'onesignal_templates_contents.geo_id');
    }
    public function scopeSettings(Builder $builder): void
    {
        $builder->leftJoin('onesignal_templates_single_settings', 'onesignal_templates_single_settings.onesignal_template_id', '=', 'onesignal_templates.id');
        $builder->leftJoin('onesignal_templates_regular_settings', 'onesignal_templates_regular_settings.onesignal_template_id', '=', 'onesignal_templates.id');
        $builder->leftJoin('onesignal_templates_delayed_settings', 'onesignal_templates_delayed_settings.onesignal_template_id', '=', 'onesignal_templates.id');
    }

    public function scopeNotifications(Builder $builder): void
    {
        $builder->leftJoin('onesignal_notifications', 'onesignal_notifications.onesignal_template_id', '=', 'onesignal_templates.id');
    }

    private function isTableJoined(Builder $builder, string $table): bool
    {
        return collect($builder->getQuery()->joins)->pluck('table')->contains($table);
    }
}
