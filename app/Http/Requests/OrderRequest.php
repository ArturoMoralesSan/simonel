<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
  

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'status' => 'required|max:20',
        ];

        for($i = 1; $i <= $this->payments_count; $i++) {
            $rules['payment' . $i . '_pago'] = 'required';
            $rules['payment' . $i . '_cost'] = 'required';
        }
        return $rules;
    }
}
