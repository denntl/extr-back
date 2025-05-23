<?php

namespace App\Services\Client\User\DTO;

use App\Models\User;

class UserCompanyOwnerTeamLeadDTO
{
    public function __construct(
        public ?User $user,
        public ?User $teamLead,
        public User $companyOwner,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user?->toArray(),
            'team_lead' => $this->teamLead?->toArray(),
            'company_owner' => $this->companyOwner->toArray(),
        ];
    }
    public function getIds(): array
    {
        return [
            'user' => $this->user?->id,
            'team_lead' => $this->teamLead?->id,
            'company_owner' => $this->companyOwner->id,
        ];
    }

    public function unique(): self
    {
        if ($this->user->id === $this->teamLead?->id) {
            $this->user = null;
        }

        if ($this->user?->id === $this->companyOwner->id) {
            $this->user = null;
        }

        if ($this->teamLead?->id === $this->companyOwner->id) {
            $this->teamLead = null;
        }

        return $this;
    }

    public function getById(int $id): ?User
    {
        foreach ($this as $user) {
            if ($user?->id === $id) {
                return $user;
            }
        }
        return null;
    }
}
