<?php

namespace App\Rules\Roles;

use Illuminate\Contracts\Validation\ValidationRule;

class DestroyRule implements ValidationRule
{
    public function __construct(protected int $roleId)
    {
    }

    public function validate($attribute, $value, $fail): void
    {
        if (is_array($value) && in_array($this->roleId, $value)) {
            $fail(__('roles.error.remove_with_users'));
        }
    }
}
