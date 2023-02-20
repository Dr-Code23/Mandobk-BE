<?php

namespace App\Http\Requests\Api\V1\Profile;

use App\Traits\Translatable;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Password;

class ProfileRequest extends FormRequest
{
    use Translatable;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return [
            'full_name' => ['required'],
            'password' => [
                'sometimes',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'avatar' => ['sometimes', 'image', 'mimes:png,jpg', 'max:2048'],
            'phone' => ['required', 'unique:users,phone,' . Auth::id() . ',id']
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => $this->translateErrorMessage('full_name', 'required'),
            'avatar.required' => $this->translateErrorMessage('avatar', 'required'),
            'avatar.image' => $this->translateErrorMessage('avatar', 'image'),
            'avatar.mimes' => $this->translateErrorMessage('avatar', 'mimes'),
            'avatar.max' => $this->translateErrorMessage('avatar', 'max.file'),
            'phone.required' => $this->translateErrorMessage('phone', 'required'),
            'phone.unique' => $this->translateErrorMessage('phone', 'unique')
        ];
    }
}