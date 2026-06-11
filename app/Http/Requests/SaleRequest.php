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

            'client_id' => ['required', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0'],
            'products.*.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.iva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.subtotal' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [

            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'products.required' => 'Debe agregar al menos un producto.',
            'products.array' => 'La lista de productos es inválida.',
            'products.min' => 'Debe agregar al menos un producto.',
            'products.*.product_id.required' => 'Debe seleccionar un producto.',
            'products.*.product_id.exists' => 'El producto seleccionado no existe.',
            'products.*.quantity.required' =>  'La cantidad es obligatoria.',
            'products.*.quantity.numeric' => 'La cantidad debe ser numérica.',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a cero.',
            'products.*.unit_price.required' => 'El precio es obligatorio.',
            'products.*.unit_price.numeric' => 'El precio debe ser numérico.',
            'products.*.unit_price.min' => 'El precio no puede ser negativo.',
            'products.*.discount.numeric' => 'El descuento debe ser numérico.',
            'products.*.discount.max' => 'El descuento no puede ser mayor a 100%.',
            'products.*.iva.numeric' => 'El IVA debe ser numérico.',
            'products.*.iva.max' => 'El IVA no puede ser mayor a 100%.',
        ];
    }
}
