<?php

namespace App\Services\Common\Auth\DTO;

class AccessDTO
{
    public function __construct(readonly bool $isAdmin, readonly array $permissions)
    {
    }

    public function toArray(): array
    {
        return [
            'isAdmin' => $this->isAdmin,
            'permissions' => $this->permissions
        ];
    }
}
