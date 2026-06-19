<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRecipeRequest extends FormRequest
{

    public function rules()
    {
        $rules = [
            'product_id' => 'required|exists:manufactured_products,id',
            'yield_quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string',
            'unit' => 'required|string',
        ];

        for ($i = 1; $i <= $this->item_count; $i++) {
            $rules['item'.$i.'_raw_material_id'] = 'required|exists:raw_materials,id';
            $rules['item'.$i.'_quantity'] = 'required|numeric|min:0.001';
            $rules['item'.$i.'_unit'] = 'required|string|max:50';
            $rules['item'.$i.'_waste_percent'] = 'nullable|numeric|min:0|max:100';
        }

        return $rules;

    }
}
