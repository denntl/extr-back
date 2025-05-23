<?php

namespace App\Http\Requests\Admin\Client\Team;

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
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'team_lead_id' => $this->input('teamLeadId'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:15'],
            'members' => ['sometimes', 'array'],
            'team_lead_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}
