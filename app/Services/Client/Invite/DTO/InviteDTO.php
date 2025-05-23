<?php

namespace App\Services\Client\Invite\DTO;

class InviteDTO
{
    public function __construct(public string $key, public string $expireAt)
    {
    }
}
