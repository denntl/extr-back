<?php

namespace App\Models\Traits;

/**
 * Trait WithNumberExternalId
 * @package App\Models\Traits
 *
 * @property int $company_id
 */
trait WithNumberExternalId
{
    /**
     * @return int|string
     */
    public function getExternalId(): int|string
    {
        return self::where('company_id', $this->company_id)->max('public_id') + 1;
    }
}
