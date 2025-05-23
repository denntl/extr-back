<?php

namespace App\Services\Manage\User\DTO;

class NewApplicationOwnersDTO
{
    /**
     * @param int $applicationId
     * @param int $userId
     */
    public function __construct(
        public int $applicationId,
        public int $userId
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            applicationId: $data['applicationId'],
            userId: $data['userId']
        );
    }
    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return [
            'applicationId' => $this->applicationId,
            'userId' => $this->userId,
        ];
    }
}
