<?php

namespace App\Http\Requests\Admin\Manage\PushTemplate;

use App\Enums\Application\Geo;
use App\Enums\PushTemplate\Event;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'geo' => ['array'],
            'geo.*' => new InEnum(Geo::class),
            'events' => ['array'],
            'events.*' => new InEnum(Event::class),
            'is_active' => 'boolean',
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'icon' => ['required', 'string'],
            'image'  => ['required', 'string'],
            'link' => ['string'],
        ];
    }
}
