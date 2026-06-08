<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ];
    }


    /**
     * Validate that the current password is correct.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->has('current_password') &&
                $this->current_password &&
                ! Hash::check($this->current_password, Auth::user()->password)
            ) {
                $validator->errors()->add('current_password', 'La contraseña actual es incorrecta.');
            }
        });
    }
}
