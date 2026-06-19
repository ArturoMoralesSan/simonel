<?php

namespace App\Http\Requests;
use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;


class WarehouseRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', new NotUppercase, new NotLowercase, 'max:180'],
            'warehouse_type' => ['required', 'max:180'],
        ];
    }
}
