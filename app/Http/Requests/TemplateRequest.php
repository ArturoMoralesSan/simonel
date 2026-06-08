<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class TemplateRequest extends FormRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'product_name'      => 'required|max:80',
            'base_price'        => 'required|numeric',
            'profit_percentage' => 'required|numeric',
            'has_inventory'     => 'required',
        ];

        if ($this->input('mode') === 'advanced') {
            $rules['type_id']         = 'required';
            $rules['product_id']      = 'required';
            $rules['cut_id']          = 'required';
            $rules['width']           = 'required|numeric';
            $rules['height']          = 'required|numeric';
            $rules['quantity_product']= 'required|numeric';
        }

        return $rules;
    }
}
