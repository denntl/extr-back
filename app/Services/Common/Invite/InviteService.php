<?php

namespace App\Services\Common\Invite;

use App\Enums\Invite\ActionName;
use App\Models\Invite;
use App\Services\Common\Invite\DTO\InviteDTO;
use App\Services\Common\Invite\Exceptions\InviteIsNotFoundException;

class InviteService
{
    /**
     * @throws InviteIsNotFoundException
     */
    public function getByKey(string $key): InviteDTO
    {
        $invite = Invite::query()
            ->where('key', $key)
            ->where('expire_at', '>', now())
            ->where('action', ActionName::Registration->value)
            ->first();

        if (!$invite) {
            throw new InviteIsNotFoundException();
        }

        return new InviteDTO($invite->key, $invite->expire_at, $invite->company->name);
    }
}
