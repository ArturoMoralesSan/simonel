<?php

namespace App\Http\Requests;

class InitialWarehouseRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'item_count' => [
                'required',
                'integer',
                'min:1',
            ],
        ];

        for ($i = 1; $i <= (int) $this->input('item_count', 0); $i++) {

            $rules["item{$i}_type"] = [
                'required',
                'in:raw_material,product',
            ];

            $rules["item{$i}_warehouse_id"] = [
                'required',
                'exists:warehouses,id',
            ];

            $rules["item{$i}_quantity"] = [
                'required',
                'numeric',
                'gt:0',
            ];

            /*
             * Costo de fabricación.
             * Este valor viene directamente del request.
             */
            $rules["item{$i}_manufacturing_cost"] = [
                'required_if:item' . $i . '_type,product',
                'numeric',
                'gte:0',
            ];

            /*
             * Precio público.
             * Este valor viene directamente del request.
             */
            $rules["item{$i}_public_price"] = [
                'required',
                'numeric',
                'gte:0',
            ];

            $rules["item{$i}_expiration_date"] = [
                'nullable',
                'date',
            ];

            /*
             * MATERIA PRIMA
             */
            $type = $this->input("item{$i}_type");

            if ($type === 'raw_material') {

                $rules["item{$i}_raw_material_id"] = [
                    'required',
                    'exists:raw_materials,id',
                ];
            }

            /*
             * PRODUCTO
             *
             * IMPORTANTE:
             * item_product_id contiene el ID de manufactured_products.
             *
             * Después de encontrarlo se crea un registro en products
             * y ese nuevo products.id se utiliza para product_lots.
             */
            if ($type === 'product') {

                $rules["item{$i}_product_id"] = [
                    'required',
                    'exists:manufactured_products,id',
                ];
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [

            'item_count.required' =>
                'Debes indicar la cantidad de lotes.',

            'item_count.integer' =>
                'La cantidad de lotes debe ser un número entero.',

            'item_count.min' =>
                'Debes registrar al menos un lote.',
        ];

        for ($i = 1; $i <= (int) $this->input('item_count', 0); $i++) {

            $messages["item{$i}_type.required"] =
                "Debes seleccionar el tipo del lote {$i}.";

            $messages["item{$i}_type.in"] =
                "El tipo seleccionado en el lote {$i} no es válido.";

            $messages["item{$i}_warehouse_id.required"] =
                "Debes seleccionar el almacén del lote {$i}.";

            $messages["item{$i}_warehouse_id.exists"] =
                "El almacén seleccionado en el lote {$i} no existe.";

            $messages["item{$i}_quantity.required"] =
                "Debes indicar la cantidad del lote {$i}.";

            $messages["item{$i}_quantity.numeric"] =
                "La cantidad del lote {$i} debe ser numérica.";

            $messages["item{$i}_quantity.gt"] =
                "La cantidad del lote {$i} debe ser mayor a cero.";

            /*
             * Manufacturing cost
             */
            $messages["item{$i}_manufacturing_cost.required_if"] =
                "Debes indicar el costo de fabricación del lote {$i}.";

            $messages["item{$i}_manufacturing_cost.numeric"] =
                "El costo de fabricación del lote {$i} debe ser numérico.";

            $messages["item{$i}_manufacturing_cost.gte"] =
                "El costo de fabricación del lote {$i} no puede ser negativo.";

            /*
             * Public price
             */
            $messages["item{$i}_public_price.required"] =
                "Debes indicar el precio público del lote {$i}.";

            $messages["item{$i}_public_price.numeric"] =
                "El precio público del lote {$i} debe ser numérico.";

            $messages["item{$i}_public_price.gte"] =
                "El precio público del lote {$i} no puede ser negativo.";

            $messages["item{$i}_expiration_date.date"] =
                "La fecha de caducidad del lote {$i} no es válida.";

            /*
             * Raw material
             */
            $messages["item{$i}_raw_material_id.required"] =
                "Debes seleccionar la materia prima del lote {$i}.";

            $messages["item{$i}_raw_material_id.exists"] =
                "La materia prima seleccionada en el lote {$i} no existe.";

            /*
             * Manufactured product
             */
            $messages["item{$i}_product_id.required"] =
                "Debes seleccionar el producto del lote {$i}.";

            $messages["item{$i}_product_id.exists"] =
                "El producto seleccionado del lote {$i} no existe.";
        }

        return $messages;
    }
}