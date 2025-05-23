<?php

namespace App\Rules\Admin\Manage\User;

use App\Services\Manage\User\UserService;
use Illuminate\Contracts\Validation\ValidationRule;

class DeactivateRule implements ValidationRule
{
    public function __construct(
        protected int $userId,
        protected bool $status,
    ) {
    }

    public function validate($attribute, $value, $fail): void
    {
        /**
         * @var UserService $userService
         */
        $userService = app(UserService::class);
        if (
            is_array($value)
            && !$userService->canDeactivateUser($this->userId, $value)
            && $this->status === false
        ) {
            $fail(__('user-actions.deactivating-suggestion'));
        }
    }
}
