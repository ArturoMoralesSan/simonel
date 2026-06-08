<?php

namespace App\Http\Requests;

class ProductionOrderRequest extends FormRequest
{
    public function rules()
    {
        $rules = [

            'issue_date'     => 'required|date',
            'delivery_date'  => 'required|date',
            'item_count'     => 'required|integer|min:1',
            'notes'          => 'nullable|max:2000',

        ];

        for ($i = 1; $i <= $this->item_count; $i++) {
            $rules['item' . $i . '_recipes_id'] = 'required|exists:product_recipes,id';
            $rules['item' . $i . '_quantity'] = 'required|numeric|min:0.001';
        }

        return $rules;
    }
}
