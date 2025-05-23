<?php

namespace App\Http\Requests\Admin\Manage\NotificationTemplate;

use App\Enums\NotificationTemplate\Entity;
use App\Enums\NotificationTemplate\Event;
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
            'entity' => ['required', 'int', new InEnum(Entity::class)],
            'event' => ['required', 'string', new InEnum(Event::class)],
            'isActive' => ['required', 'boolean'],
            'isAllUsers' => ['required', 'boolean'],
            'isClientShow' => ['required', 'boolean'],
            'roles' => ['array'],
            'roles.*' => ['int', 'exists:roles,id'],
        ];
    }
}
