<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ApplicationComment
 *
 * @property int $id
 * @property string $author_name
 * @property string $text
 * @property int $stars
 * @property string $lang
 * @property string $icon
 * @property int $created_by
 * @property string|null $answer
 * @property Carbon|null $date
 * @property int|null $application_id
 * @property int|null $origin_id
 * @property int $likes
 * @property string|null $answer_author
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $author
 * @property-read Application|null $application
 * @property-read ApplicationComment|null $origin
 * @method static Builder|ApplicationComment newModelQuery()
 * @method static Builder|ApplicationComment newQuery()
 * @method static Builder|ApplicationComment query()
 * @method static Builder|ApplicationComment whereId($value)
 * @method static Builder|ApplicationComment whereAuthorName($value)
 * @method static Builder|ApplicationComment whereText($value)
 * @method static Builder|ApplicationComment whereStars($value)
 * @method static Builder|ApplicationComment whereLang($value)
 * @method static Builder|ApplicationComment whereIcon($value)
 * @method static Builder|ApplicationComment whereCreatedBy($value)
 * @method static Builder|ApplicationComment whereAnswer($value)
 * @method static Builder|ApplicationComment whereDate($value)
 * @method static Builder|ApplicationComment whereApplicationId($value)
 * @method static Builder|ApplicationComment whereOriginId($value)
 * @method static Builder|ApplicationComment whereLikes($value)
 * @method static Builder|ApplicationComment whereAnswerAuthor($value)
 * @method static Builder|ApplicationComment whereCreatedAt($value)
 * @method static Builder|ApplicationComment whereUpdatedAt($value)
 */
class ApplicationComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'author_name',
        'text',
        'stars',
        'lang',
        'icon',
        'created_by',
        'answer',
        'date',
        'application_id',
        'origin_id',
        'likes',
        'answer_author',
    ];

    protected $hidden = [
        'application_id',
    ];

    public function scopePublicId(Builder $query): void
    {
        $query->join('applications', 'applications.id', '=', 'application_comments.application_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function origin()
    {
        return $this->belongsTo(ApplicationComment::class, 'origin_id');
    }
}
