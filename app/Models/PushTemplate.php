<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PushTemplateFactory
 *
 * @property int $id
 * @property string $name
 * @property array|string $geo
 * @property array|string $events
 * @property int|null $created_by
 * @property boolean $is_active
 * @property string $title
 * @property string $content
 * @property string $icon
 * @property string $image
 * @property string|null $link
 * @property-read User|null $createdBy
 *
 */
class PushTemplate extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'geo',
        'events',
        'created_by',
        'is_active',
        'title',
        'content',
        'icon',
        'image',
        'link',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    protected $casts = [
        'geo' => 'array',
        'events' => 'array',
    ];

    public function scopeCreatedBy(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'push_templates.created_by');
    }
}
