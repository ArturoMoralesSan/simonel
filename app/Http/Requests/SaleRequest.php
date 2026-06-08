<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:users,id',
            'products'  => 'required|array|min:1',
            'products.*.template_id' => 'required|exists:product_templates,id',
            'products.*.quantity'    => 'required|numeric|min:1',
            'products.*.unit_price'  => 'required|numeric|min:0',
            'products.*.discount'    => 'nullable|numeric|min:0|max:100',
            'products.*.iva'         => 'nullable|numeric|min:0|max:100',
            'products.*.custom_name' => 'nullable|string|max:255',
            'comment'   => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'products.required'  => 'Debe añadir al menos un producto.',
            'products.*.template_id.required' => 'Cada producto debe tener una plantilla.',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
