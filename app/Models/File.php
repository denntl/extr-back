<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\File
 *
 * @property int $id
 * @property int $company_id
 * @property int $uploaded_by
 * @property string $path
 * @property string $original_name
 * @property string $mime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File whereCompanyId($value)
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereMime($value)
 * @method static Builder|File whereOriginalName($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File wherePath($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUploadedBy($value)
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'uploaded_by',
        'path',
        'original_name',
        'mime',
    ];
}
