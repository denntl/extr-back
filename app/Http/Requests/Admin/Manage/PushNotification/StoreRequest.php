<?php

namespace App\Http\Requests\Admin\Manage\PushNotification;

use App\Enums\Application\Geo;
use App\Enums\PushNotification\Type;
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
            'type' => ['required', 'integer', new InEnum(Type::class)],
            'name' => ['required', 'string', 'max:255'],
            'push_template_id' => ['nullable', 'integer', 'exists:push_templates,id'],
            'application_id' => ['integer', 'exists:applications,id'],
            'date' => ['string', 'nullable'],
            'time' => ['required', 'string'],
            'geo' => ['array'],
            'geo.*' => new InEnum(Geo::class),
            'events' => ['array'],
            'events.*' => new InEnum(Event::class),
            'is_active' => 'boolean',
            'is_delayed' => 'boolean',
            'title' => ['string', 'max:255'],
            'content' => ['string'],
            'icon' => ['string'],
            'image'  => ['string'],
            'link' => ['string'],
        ];
    }
}
