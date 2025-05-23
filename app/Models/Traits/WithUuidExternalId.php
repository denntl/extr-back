<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * Trait WithNumberExternalId
 * @package App\Models\Traits
 *
 * @property int $company_id
 */
trait WithUuidExternalId
{
    /**
     * @return int|string
     */
    public function getExternalId(): int|string
    {
        return Str::uuid();
    }
}
