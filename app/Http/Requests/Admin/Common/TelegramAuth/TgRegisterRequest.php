<?php

namespace App\Http\Requests\Admin\Common\TelegramAuth;

use Illuminate\Foundation\Http\FormRequest;

class TgRegisterRequest extends FormRequest
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
            'auth_date' => ['required', 'integer'],
            'first_name' => ['required', 'string'],
            'id' => ['required', 'integer', 'unique:users,telegram_id'],
            'last_name' => ['sometimes', 'string'],
            'username' => ['required', 'string'],
            'hash' => ['required', 'string'],
            'photo_url' => ['sometimes', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => __('common.auth.unique_tg_id'),
        ];
    }
}
