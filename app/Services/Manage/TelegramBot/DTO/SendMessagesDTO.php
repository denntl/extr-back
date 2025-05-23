<?php

namespace App\Services\Manage\TelegramBot\DTO;

class SendMessagesDTO
{
    public function __construct(
        public array $companies,
        public string $message,
        public array $roles,
        public bool $isAllUsers,
        public bool $isAllCompanies
    ) {
    }
}
