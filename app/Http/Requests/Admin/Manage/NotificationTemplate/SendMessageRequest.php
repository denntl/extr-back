<?php

namespace App\Http\Requests\Admin\Manage\NotificationTemplate;

use App\Enums\NotificationTemplate\Entity;
use App\Enums\NotificationTemplate\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
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
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'isAllUsers' => ['required', 'boolean'],
            'isAllCompanies' => ['required', 'boolean'],
            'roles' => ['array'],
            'roles.*' => ['int', 'exists:roles,id'],
            'companies' => ['array'],
            'companies.*' => ['int', 'exists:companies,id'],
        ];
    }
}
