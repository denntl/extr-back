<?php

namespace App\Http\Requests\Site;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PreviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'geo' => 'nullable|string',
            'platform_type' => 'nullable|numeric',
            'landing_type' => 'nullable|numeric',
            'category' => 'nullable|numeric',
            'white_type' => 'nullable|numeric',
            'name' => 'nullable|string|min:5|max:255',
            'domain_id' => 'nullable|numeric|exists:domains,id',
            'subdomain' => ['nullable', 'string', 'max:255'],
            'pixel_id' => 'nullable|string|max:255',
            'pixel_key' => 'nullable|string|max:255',
            'link' => 'nullable|string',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'downloads_count' => 'nullable|string|max:20',
            'rating' => 'nullable|numeric',
            'app_name' => 'nullable|string|max:255',
            'developer_name' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:5',
            'files' => 'nullable|array',
            'files.*' => 'exists:files,id',
            'owner_id' => 'sometimes|numeric|exists:users,id',
            'company_id' => 'sometimes|string|exists:companies,public_id',
            'display_app_bar' => 'sometimes|numeric',
            'topApplicationIds' => 'sometimes|nullable|array',
            'topApplicationIds.*' => 'sometimes|numeric|exists:applications,public_id',
            'display_top_bar' => 'sometimes|boolean',
            'uuid' => 'sometimes|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
