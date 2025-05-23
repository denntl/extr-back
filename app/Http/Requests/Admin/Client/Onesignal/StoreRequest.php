<?php

namespace App\Http\Requests\Admin\Client\Onesignal;

use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'onesignal_template_id' => ['nullable', 'integer', 'exists:onesignal_templates,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', new InEnum(Type::class)],
            'scheduled_at' => ['nullable', 'date', 'date_format:Y-m-d H:i:s'], //only for single
            'time' => ['nullable', 'string'], //only for regular
            'days' => ['nullable', 'array', 'max:6'], //only for regular
            'days.*' => ['int', 'min:0', 'max:6'], //only for regular
            'delay' => ['nullable', 'string'], //only for delayed
            'events' => ['array'], //only for delayed
            'events.*' => new InEnum(Event::class), //only for delayed
            'is_active' => 'boolean',
            //right column
            'application_ids' => ['array', 'required'],
            'application_ids.*' => ['integer', 'exists:applications,public_id'],
            'geos' => ['array', 'required'],
            'geos.*' => ['integer', 'exists:geos,id'],
            'segments' => ['array', 'required'],
            'segments.*' => new InEnum(Event::class),
            //content
            'contents' => ['array', 'required'],
            'contents.*' => ['array'],
            'contents.*.geo' => ['int', 'exists:geos,id'],
            'contents.*.text' => ['string', 'required'],
            'contents.*.title' => ['string', 'required'],
            'contents.*.image' => ['string', 'required'],
        ];
    }

    public function messages(): array
    {
        return [
            'contents.*.text.required' => 'The text field is required for each content item.',
            'contents.*.title.required' => 'Each content item must have a title.',
            'contents.*.image.required' => 'An image is required for each content item.',

            'contents.*.text.string' => 'The text must be a string.',
            'contents.*.title.string' => 'The title must be a string.',
            'contents.*.image.string' => 'The image must be a valid string path.',
        ];
    }
}
