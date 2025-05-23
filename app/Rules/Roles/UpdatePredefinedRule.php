<?php

namespace App\Rules\Roles;

use App\Services\Manage\Role\RoleService;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Permission\Models\Role;

class UpdatePredefinedRule implements ValidationRule
{
    private Role $role;
    public function __construct(protected int $roleId)
    {
        /** @var RoleService $roleService */
        $roleService = app(RoleService::class);
        $this->role = $roleService->getRoleById($this->roleId);
    }

    public function validate($attribute, $value, $fail): void
    {
        if (
            $this->role->getAttribute('is_predefined') === true
            && $this->role->name !== $value
        ) {
            $fail(__('roles.error.update_predefined'));
        }
    }
}
