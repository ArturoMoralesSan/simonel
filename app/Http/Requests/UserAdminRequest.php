<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class UserAdminRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => ['required', new NotUppercase, new NotLowercase, 'max:180'],
            'last_name' => ['required', new NotUppercase, new NotLowercase, 'max:180'],
            'username' => ['required', 'max:180'],
            'email'     => ['required','email', 'max:60'],
            'role_id'     => ['required', 'max:60'],
            'password'  => ['required_at_create','max:80'],
        ];
    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required_at_create' => 'El campo es obligatorio.'
        ];
    }
}
