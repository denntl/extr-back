<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyNotification
 *
 * @property int $id
 * @property int $notification_template_id
 * @property int $company_id
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|CompanyNotification newModelQuery()
 * @method static Builder|CompanyNotification newQuery()
 * @method static Builder|CompanyNotification query()
 * @method static Builder|CompanyNotification whereId($value)
 * @method static Builder|CompanyNotification whereNotificationTemplateId($value)
 * @method static Builder|CompanyNotification whereCompanyId($value)
 * @method static Builder|CompanyNotification whereIsEnabled($value)
 * @method static Builder|CompanyNotification whereCreatedAt($value)
 * @method static Builder|CompanyNotification whereUpdatedAt($value)
 */
class CompanyNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_template_id',
        'company_id',
        'is_enabled',
    ];
}
