<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'manufactured_product_id' => ['required', 'max:20'],
            'cut_id' => ['required', 'max:20'],
            'vinil_cost' => 'required_unless:type_id,3,|max:10',
            'impresion_cost' => 'required_unless:type_id,3,|max:10',
            'indirect_cost' => 'required|max:10',
            'subtotal' => 'required|max:10',
            'utility' => 'required|max:10',
            'costo_total' => 'required|max:10',
            'costo_venta' => 'required|max:10',
        ];
    }
}
