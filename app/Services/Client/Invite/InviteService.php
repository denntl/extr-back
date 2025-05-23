<?php

namespace App\Services\Client\Invite;

use App\Enums\Invite\ActionName;
use App\Models\Invite;
use App\Services\Client\Invite\DTO\ActionDTO;
use App\Services\Client\Invite\DTO\InviteDTO;
use Illuminate\Support\Str;

readonly class InviteService
{
    public function __construct(private int $companyId)
    {
    }

    public function generateInvite(ActionDTO $dto): InviteDTO
    {
        $invite = Invite::query()->create([
            'key' => Str::uuid(),
            'expire_at' => now()->addMinutes($dto->expireAfter),
            'company_id' => $this->companyId,
            'action' => $dto->name->value,
            'body' => $dto->body,
            'created_by' => $dto->userId,
        ]);

        return new InviteDTO($invite->key, $invite->expire_at);
    }

    public function getByKey(string $key, ActionName $actionName, ?bool $public = true): ?Invite
    {
        $invite = Invite::query()
            ->where('key', $key)
            ->where('expire_at', '>', now())
            ->where('action', $actionName)
            ->where('company_id', $this->companyId);

        if ($public) {
            $invite->select(['key', 'expire_at']);
        }

        return $invite->first();
    }
}
