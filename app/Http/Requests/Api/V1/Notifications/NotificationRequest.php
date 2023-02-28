<?php

namespace App\Http\Requests\Api\V1\Notifications;

use App\Rules\NotificationTypeExists;
use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $notificationsTypes = array_keys(config('notifications.types'));
        return [
            'type' => ['required', new NotificationTypeExists()]
        ];
    }
}
