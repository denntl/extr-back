<?php

namespace App\Services\Client\Invite\DTO;

use App\Enums\Invite\ActionName;

class ActionDTO
{
    public function __construct(public int $userId, public ActionName $name, public ?array $body = [], public int $expireAfter = 60)
    {
    }
}
