<?php

namespace App\Http\Requests\Admin\Client\User;

use App\Rules\User\DeactivateCompanyOwnerRule;
use App\Rules\User\DeactivateRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeactivateRequest extends FormRequest
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
            'status' => [
                'required',
                'boolean',
            ],
            'companyId' => ['required', 'string', 'exists:companies,public_id'],
            'newApplicationsOwners' => [
                'present',
                'array',
                new DeactivateRule($this->id, $this->status, $this->companyId),
                new DeactivateCompanyOwnerRule($this->id, $this->status, $this->companyId),
            ],
            'newApplicationsOwners.*.applicationId' => [
                'required',
                'integer',
                'exists:applications,id'
            ],
            'newApplicationsOwners.*.userId' => [
                'required',
                'integer',
                'exists:users,id'
            ],
        ];
    }
}
