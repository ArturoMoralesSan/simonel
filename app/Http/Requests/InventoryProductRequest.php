<?php

namespace App\Http\Requests;

class InventoryProductRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id'  => 'required|max:20',
            'tag' => 'required',
            'type'       => 'required',
            'quantity'       => 'required', 
            'quantity_min'       => 'required',    
   
        ];
    }
}
