<?php

namespace App\Http\Requests\Admin\Common\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:15|min:2|unique:users,username|regex:/^[0-9A-Za-z]+$/',
            'companyName' => 'required|string|max:15|min:2',
            'telegramName' => 'nullable|string|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/',
        ];
    }
}
