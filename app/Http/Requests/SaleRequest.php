<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    
    public function rules(): array
    {
        $rules = [

            // Cliente
            'client_id' => ['required', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:2000'],

            // Productos
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0'],
            'products.*.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.iva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'products.*.subtotal' => ['nullable', 'numeric', 'min:0'],

            // Pagos
            'payments_count' => ['required', 'integer', 'min:1'],
        ];

        for ($i = 1; $i <= request('payments_count', 1); $i++) {

            $rules["payment{$i}_pago"] = [
                'required',
                'exists:payments,id',
            ];

            $rules["payment{$i}_cost"] = [
                'required',
                'numeric',
                'min:0.01',
            ];
        }

        return $rules;
    }

    /**
     * Custom validation.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $paymentsTotal = 0;

            for ($i = 1; $i <= $this->input('payments_count', 1); $i++) {
                $paymentsTotal += (float) $this->input("payment{$i}_cost", 0);
            }

            $gross = (float) $this->input('gross_amount', 0);
            $discount = (float) $this->input('discounts', 0);

            $saleTotal = $gross - $discount;

            if (abs($paymentsTotal - $saleTotal) > 0.01) {

                $validator->errors()->add(
                    'payment1_cost',
                    'La suma de los métodos de pago debe ser igual al total de la venta.'
                );
            }
        });
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [

            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',

            'products.required' => 'Debe agregar al menos un producto.',
            'products.min' => 'Debe agregar al menos un producto.',

            'products.*.product_id.required' => 'Seleccione un producto.',
            'products.*.product_id.exists' => 'El producto seleccionado no existe.',

            'products.*.quantity.required' => 'Capture la cantidad.',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a cero.',

            'products.*.unit_price.required' => 'Capture el precio.',
            'products.*.unit_price.min' => 'El precio no puede ser negativo.',

            'payment1_pago.required' => 'Debe seleccionar un método de pago.',
        ];
    }
}
