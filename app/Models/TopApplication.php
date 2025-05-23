<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $parent_application_id
 * @property int $child_application_id
 *
 * @property Application $parentApplication
 * @property Application $childApplication
 */
class TopApplication extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'parent_application_id',
        'child_application_id',
    ];

    public function parentApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'parent_application_id', 'id');
    }

    public function childApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'child_application_id', 'id');
    }
}
