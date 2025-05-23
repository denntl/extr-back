<?php

namespace App\Http\Requests\Admin\Manage\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'name' => ['required', 'unique:roles,name', 'string', 'min:2', 'max:50'],
            'permissionIds' => ['array'],
            'permissionIds.*' => ['int', 'exists:permissions,id'],

        ];
    }
}
