<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property Carbon $date
 * @property int $application_id
 * @property int $clicks
 * @property int $push_subscriptions
 * @property int $unique_clicks
 * @property int $installs
 * @property int $deposits
 * @property int $registrations
 * @property int $first_installs
 * @property int $repeated_installs
 * @property int $opens
 * @property int $repeated_opens
 * @property int $first_opens
 * @property float $ins_to_uc
 * @property float $reg_to_ins
 * @property float $dep_to_ins
 * @property float $dep_to_reg
 */
class ApplicationStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'application_id',
        'clicks',
        'push_subscriptions',
        'unique_clicks',
        'installs',
        'deposits',
        'registrations',
        'ins_to_uc',
        'reg_to_ins',
        'dep_to_ins',
        'dep_to_reg',
        'first_installs',
        'repeated_installs',
        'opens',
        'repeated_opens',
        'first_opens',
    ];

    public function scopeApplication(Builder $builder)
    {
        $builder->join('applications', 'applications.id', '=', 'application_statistics.application_id');
    }

    public function scopeOwner(Builder $builder)
    {
        if (!$builder->hasNamedScope('application')) {
            $this->scopeApplication($builder);
        }
        $builder->join('users', 'users.id', '=', 'applications.owner_id');
    }

    public function scopeGeo(Builder $builder)
    {
        $rawQuery = '(select application_geo_languages.application_id, STRING_AGG(application_geo_languages.geo, \', \') as geos'
            . ' from application_geo_languages group by application_geo_languages.application_id) app_geos';
        $builder->leftJoin(
            DB::raw($rawQuery),
            'application_statistics.application_id',
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
}
