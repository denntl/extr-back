<?php

namespace App\Http\Requests\Admin\Manage\Role;

use App\Rules\Roles\DestroyRule;
use App\Services\Manage\Role\RoleService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        /** @var RoleService $roleService */
        $roleService = app(RoleService::class);
        $role = $roleService->getRoleById($this->id);

        if ($role->getAttribute('is_predefined') === true) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'roleId' => __('roles.error.remove_predefined'),
            ]);
        }

        return [
            'users' => 'array',
            'users.*.userId' => [
                'required',
                'int',
                'exists:users,id'
            ],
            'users.*.roleIds' => [
                'array',
                new DestroyRule($this->id)
            ],
            'users.*.roleIds.*' => ['int', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'users.*.roleIds' => [
                'roleId' => __('roles.error.remove_with_users')
            ],
        ];
    }
}
