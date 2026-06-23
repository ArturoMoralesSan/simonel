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
            'supplier_id' => ['required', 'max:100'],
            'entry_date' => ['required', 'max:100'],
            'quantity' => ['required', 'max:10'],
            'cost' => ['required','max:10'],
        ];
    }
}
