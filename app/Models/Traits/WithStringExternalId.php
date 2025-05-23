<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait WithNumberExternalId
 * @package App\Models\Traits
 *
 * @property int $company_id
 */
trait WithStringExternalId
{
    /**
     * @return int|string
     */
    public function getExternalId(): int|string
    {
        mt_srand(round(microtime(true) * 1000));

        return Str::random(8);
    }
}
