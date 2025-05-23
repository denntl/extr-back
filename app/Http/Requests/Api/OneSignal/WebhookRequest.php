<?php

namespace App\Http\Requests\Api\OneSignal;

use App\Enums\OneSignal\NotificationEventType;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event' => ['required', 'string', new InEnum(NotificationEventType::class)],
            'notificationId' => ['required', 'string', 'exists:onesignal_notifications,onesignal_notification_id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([]));
    }
}
