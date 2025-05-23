<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $application_id
 * @property string $geo
 * @property string $language
 */
class ApplicationGeoLanguage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'geo',
        'language'
    ];
}
