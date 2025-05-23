<?php

namespace App\Rules\Admin\Manage\User;

use App\Services\Manage\User\UserService;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateStatusRule implements ValidationRule
{
    public function __construct(
        protected int $userId,
    ) {
    }

    public function validate($attribute, $value, $fail): void
    {
        $userService = app(UserService::class);
        if (
            $userService->getActiveApplications($this->userId)->count() !== 0
            && $value === 0
        ) {
            $fail(__('user-actions.deactivating-suggestion'));
        }
    }
}
