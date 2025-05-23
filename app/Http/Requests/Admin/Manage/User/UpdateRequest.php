<?php

namespace App\Http\Requests\Admin\Manage\User;

use App\Rules\Admin\Manage\User\UpdateStatusRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'email' => ['required', 'string'],
            'is_employee' => ['required', 'boolean'],
            'roles' => ['array'],
            'roles.*' => ['int', 'exists:roles,id'],
            'status' => [
                'required',
                'integer',
                new UpdateStatusRule($this->id),
            ],
            'username' => ['required', 'string'],
            'name' => ['required', 'string'],
            'password' => 'string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/',
        ];
    }
}
