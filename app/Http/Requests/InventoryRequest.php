<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
   
    public function rules()
    {
        $rules = [
            'client_id' => ['required', 'exists:users,id'],
        ];

        $productCount = $this->input('product_count', 1);
        for ($i = 1; $i <= $productCount; $i++) {
            $rules['inventory' . $i . '_product_id'] = ['required', 'numeric'];
            $rules['inventory' . $i . '_quantity'] = ['required', 'numeric', 'min:0.1'];
            $rules['inventory' . $i . '_date'] = ['required', 'date'];

            if ($this->input('type') === 'salida') {
                $rules['inventory' . $i . '_sale_id'] = ['nullable', 'exists:sales,id'];
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Debes seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'type.required' => 'Debes seleccionar un tipo de movimiento.',
            'type.in' => 'El tipo debe ser "entrada" o "salida".',
            'product_count.required' => 'Debes agregar al menos un producto.',
            'product_count.min' => 'Debes agregar al menos un producto.',
            '*.required' => 'Este campo es obligatorio.',
            '*.exists' => 'El valor seleccionado no es válido.',
            '*.numeric' => 'Este campo debe ser un número.',
            '*.min' => 'El valor mínimo permitido es 1.',
            '*.date' => 'Debes ingresar una fecha válida.',
        ];
    }
}
