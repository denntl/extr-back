<?php

namespace App\Http\Requests\Admin\Manage\Role;

use App\Rules\Roles\UpdatePredefinedRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($this->id),
                'string',
                'min:2',
                'max:50',
                new UpdatePredefinedRule($this->id),
            ],
            'permissionIds' => ['array'],
            'permissionIds.*' => ['int', 'exists:permissions,id'],
        ];
    }
}
