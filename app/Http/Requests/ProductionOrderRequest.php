<?php

namespace App\Http\Requests;

class ProductionOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $rules = [

            /*
            |--------------------------------------------------------------------------
            | DATOS GENERALES
            |--------------------------------------------------------------------------
            */

            'issue_date'     => ['required', 'date'],
            'delivery_date'  => ['required','date'],
            'item_count'     => ['required','integer','min:1'],
            'notes' => ['nullable','max:2000'],


            /*
            |--------------------------------------------------------------------------
            | PESO DEL COMBO
            |--------------------------------------------------------------------------
            */

            'combo_weight' => ['required','numeric','min:0'],

            /*
            |--------------------------------------------------------------------------
            | DESPIECE / YIELDS
            |--------------------------------------------------------------------------
            */

            'yields' => ['nullable','array'],
            'yields.*.type' => ['required','in:carne,grasa,hueso,cuero,desjugue,merma'],
            'yields.*.quantity' => ['nullable','numeric','min:0'],
            'yields.*.unit' => ['required', 'in:kg,%,pza,lt'],
        ];


        /*
        |--------------------------------------------------------------------------
        | ITEMS DE PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        for ($i = 1; $i <= $this->item_count; $i++) {
            $rules['item' . $i . '_recipes_id'] = [ 'required', 'exists:product_recipes,id'];
            $rules['item' . $i . '_quantity'] = ['required','numeric','min:0.001'];
        }

        return $rules;
    }


    /**
     * Custom validation messages.
     */
    public function messages()
    {
        return [
            'issue_date.required' => 'La fecha de elaboración es requerida.',
            'delivery_date.required' => 'La fecha de entrega es requerida.',
            'item_count.required' => 'Debe agregar al menos un producto.',
            'item_count.min' => 'Debe agregar al menos un producto.',
            'item*_recipes_id.required' => 'Debe seleccionar una receta.',
            'yields.array' => 'El despiece debe tener un formato válido.',
            'yields.*.type.in' => 'El tipo de despiece no es válido.',
            'yields.*.quantity.numeric' => 'La cantidad del despiece debe ser numérica.',
            'yields.*.unit.in' => 'La unidad del despiece no es válida.',
        ];
    }

}
