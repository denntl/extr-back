<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnesignalTemplateSingleSettings extends Model
{
    use HasFactory;

    protected $table = 'onesignal_templates_single_settings';
    public $timestamps = false;

    protected $fillable = [
        'onesignal_template_id',
        'scheduled_at',
        'handled_at',
    ];

    public function onesignalTemplate(): BelongsTo
    {
        return $this->belongsTo(OnesignalTemplate::class);
    }
}
