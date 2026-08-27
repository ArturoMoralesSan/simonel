<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'business_name' => 'required|string|max:100',
            'trade_name'    => 'required|string|max:100',
            'contact_name'  => 'required|string|max:100',
            'rfc'           => 'nullable|string|max:20',
            'phone'         => 'required|digits:10',
            'email'         => 'nullable|max:100',
            'address'       => 'nullable|string|max:1000',
            'notes'         => 'nullable|string|max:2000',
        ];
    }
}
