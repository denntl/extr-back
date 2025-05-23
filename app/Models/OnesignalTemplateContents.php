<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnesignalTemplateContents extends Model
{
    use HasFactory;

    protected $table = 'onesignal_templates_contents';

    protected $fillable = [
        'onesignal_template_id',
        'geo_id',
        'title',
        'text',
        'image'
    ];

    public function onesignalTemplate(): BelongsTo
    {
        return $this->belongsTo(OnesignalTemplate::class, 'onesignal_template_id', 'id');
    }

    public function geo(): BelongsTo
    {
        return $this->belongsTo(Geo::class, 'geo_id', 'id');
    }
}
