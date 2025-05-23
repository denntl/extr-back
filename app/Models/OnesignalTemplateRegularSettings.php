<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnesignalTemplateRegularSettings extends Model
{
    protected $table = 'onesignal_templates_regular_settings';
    public $timestamps = false;

    protected $casts = [
        'days' => 'array'
    ];

    protected $fillable = [
        'onesignal_template_id',
        'time',
        'days',
        'handled_at'
    ];

    public function onesignalTemplate(): BelongsTo
    {
        return $this->belongsTo(OnesignalTemplate::class);
    }
}
