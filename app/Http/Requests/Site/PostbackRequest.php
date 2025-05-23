<?php

namespace App\Http\Requests\Site;

use App\Enums\PwaEvents\Event;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PostbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'exists:pwa_client_clicks,external_id'],
            'status' => ['required', 'string', new InEnum(Event::class)],
        ];
    }
}
