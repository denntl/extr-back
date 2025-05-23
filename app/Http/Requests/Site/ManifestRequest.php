<?php

namespace App\Http\Requests\Site;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ManifestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_uuid' => ['required', 'string', 'exists:applications,uuid'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([], 404));
    }
}
