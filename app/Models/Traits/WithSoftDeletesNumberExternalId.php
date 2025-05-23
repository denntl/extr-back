<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait WithNumberExternalId
 * @package App\Models\Traits
 *
 * @property int $company_id
 * @method static \Illuminate\Database\Eloquent\Builder|static withTrashed()
 */
trait WithSoftDeletesNumberExternalId
{
    /**
     * @return int|string
     */
    public function getExternalId(): int|string
    {
        return self::where('company_id', $this->company_id)->withTrashed()->max('public_id') + 1;
    }
}
