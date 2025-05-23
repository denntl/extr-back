<?php

namespace App\Models\Traits\Contracts;

interface WithExternalIdInterface
{
    public function getExternalId(): int|string;
}
