<?php

namespace App\Http\Requests;

class PurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'supplier_id'    => 'required|exists:suppliers,id',
            'purchase_date'  => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:1000',
            'item_count'     => 'required|integer|min:1',
        ];

        $itemCount = request('item_count', 0);

        for ($i = 1; $i <= $itemCount; $i++) {

            $rules["item{$i}_raw_material_id"] = [
                'required',
                'exists:raw_materials,id'
            ];

            $rules["item{$i}_warehouse_id"] = [
                'required',
                'exists:warehouses,id'
            ];

            $rules["item{$i}_quantity"] = [
                'required',
                'numeric',
                'min:0.001'
            ];

            $rules["item{$i}_unit_cost"] = [
                'required',
                'numeric',
                'min:0'
            ];

            $rules["item{$i}_supplier_lot"] = [
                'nullable',
                'string',
                'max:100'
            ];

            $rules["item{$i}_expiration_date"] = [
                'nullable',
                'date'
            ];
        }

        return $rules;
    }
}
