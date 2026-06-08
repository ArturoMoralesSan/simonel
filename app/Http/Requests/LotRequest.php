<?php

namespace App\Http\Requests;

class LotRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'raw_material_id' => ['required', 'max:5'],
            'warehouse_id' => ['required', 'max:5'],
            'lot_number' => ['required','max:10'],
            'supplier_lot' => ['required', 'max:100'],
            'entry_date' => ['required', 'max:100'],
            'initial_quantity' => ['required', 'max:10'],
            'available_quantity' => ['required','max:10'],
            'cost' => ['required','max:10'],
            'status' => ['required', 'max:100'],
        ];
    }
}
