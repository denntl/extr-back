<?php

namespace App\Http\Requests\Admin\Manage\User;

use App\Rules\Admin\Manage\User\DeactivateRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
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
            'status' => 'required|integer',
            'newApplicationsOwners' => [
                'present',
                'array',
                new DeactivateRule($this->id, $this->status),
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
