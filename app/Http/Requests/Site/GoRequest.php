<?php

namespace App\Http\Requests\Site;

use App\Rules\ComCookieRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'com' => ['required', 'string', 'exists:applications,uuid'],
            'externalId' => ['nullable', 'string', 'uuid'],
            'onesignal' => ['nullable', 'string']
        ];
    }

    protected function getRedirectUrl()
    {
        return '/';
    }
}
