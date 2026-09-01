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

            'purchase_date'  => 'required|date|before_or_equal:today',

            'invoice_number' => 'nullable|string|max:100',

            'notes'          => 'nullable|string|max:1000',

            'item_count'     => 'required|integer|min:1',

            // DESHUESE DEL COMBO
            'deshuese_pulpa'            => 'nullable|numeric|min:0|max:100',
            'deshuese_hueso'            => 'nullable|numeric|min:0|max:100',
            'deshuese_lonja'            => 'nullable|numeric|min:0|max:100',
            'deshuese_cuero_planchado'  => 'nullable|numeric|min:0|max:100',
            'deshuese_chamorro'         => 'nullable|numeric|min:0|max:100',

            'deshuese_costilla'         => 'nullable|numeric|min:0|max:100',
            'deshuese_piernas'          => 'nullable|numeric|min:0|max:100',
            'deshuese_paleta'           => 'nullable|numeric|min:0|max:100',
            'deshuese_lomo'             => 'nullable|numeric|min:0|max:100',
            'deshuese_espinazo'         => 'nullable|numeric|min:0|max:100',
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

    public function messages()
    {
        return [

            'purchase_date.before_or_equal' =>
                'La fecha de compra no puede ser posterior a hoy.',

            'item_count.min' =>
                'Debes registrar al menos un elemento.',

            'deshuese_pulpa.numeric' =>
                'La Pulpa debe ser un número.',

            'deshuese_pulpa.min' =>
                'La Pulpa no puede ser menor a 0%.',

            'deshuese_pulpa.max' =>
                'La Pulpa no puede ser mayor a 100%.',

            'deshuese_hueso.numeric' =>
                'El Hueso debe ser un número.',

            'deshuese_hueso.min' =>
                'El Hueso no puede ser menor a 0%.',

            'deshuese_hueso.max' =>
                'El Hueso no puede ser mayor a 100%.',

            'deshuese_lonja.numeric' =>
                'La Lonja debe ser un número.',

            'deshuese_lonja.min' =>
                'La Lonja no puede ser menor a 0%.',

            'deshuese_lonja.max' =>
                'La Lonja no puede ser mayor a 100%.',

            'deshuese_cuero_planchado.numeric' =>
                'El Cuero planchado debe ser un número.',

            'deshuese_cuero_planchado.min' =>
                'El Cuero planchado no puede ser menor a 0%.',

            'deshuese_cuero_planchado.max' =>
                'El Cuero planchado no puede ser mayor a 100%.',

            'deshuese_chamorro.numeric' =>
                'El Chamorro debe ser un número.',

            'deshuese_chamorro.min' =>
                'El Chamorro no puede ser menor a 0%.',

            'deshuese_chamorro.max' =>
                'El Chamorro no puede ser mayor a 100%.',

            'deshuese_costilla.numeric' =>
                'La Costilla debe ser un número.',

            'deshuese_costilla.min' =>
                'La Costilla no puede ser menor a 0%.',

            'deshuese_costilla.max' =>
                'La Costilla no puede ser mayor a 100%.',

            'deshuese_piernas.numeric' =>
                'Las Piernas deben ser un número.',

            'deshuese_piernas.min' =>
                'Las Piernas no pueden ser menores a 0%.',

            'deshuese_piernas.max' =>
                'Las Piernas no pueden ser mayores a 100%.',

            'deshuese_paleta.numeric' =>
                'La Paleta debe ser un número.',

            'deshuese_paleta.min' =>
                'La Paleta no puede ser menor a 0%.',

            'deshuese_paleta.max' =>
                'La Paleta no puede ser mayor a 100%.',

            'deshuese_lomo.numeric' =>
                'El Lomo debe ser un número.',

            'deshuese_lomo.min' =>
                'El Lomo no puede ser menor a 0%.',

            'deshuese_lomo.max' =>
                'El Lomo no puede ser mayor a 100%.',

            'deshuese_espinazo.numeric' =>
                'El Espinazo debe ser un número.',

            'deshuese_espinazo.min' =>
                'El Espinazo no puede ser menor a 0%.',

            'deshuese_espinazo.max' =>
                'El Espinazo no puede ser mayor a 100%.',
        ];
    }
}
