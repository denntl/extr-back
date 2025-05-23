<?php

namespace App\Models\Traits;

trait WithExternalId
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set the public_id before saving
            $fieldNane = $user->getExternalIdName();
            $user->{$fieldNane} = $user->getExternalId();
        });
    }

    protected function getExternalIdName(): string
    {
        return $this->external_id_field ?? 'public_id';
    }
}
