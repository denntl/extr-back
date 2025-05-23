<?php

namespace App\Http\Requests\Site;

use App\Enums\PwaEvents\Event;
use App\Rules\ComCookieRule;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnalyticRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'com' => ['required', 'string', 'exists:applications,uuid'],
            't' => ['string', new InEnum(Event::class)],
            'externalId' => ['nullable', 'string', 'uuid'],
        ];
    }
}
