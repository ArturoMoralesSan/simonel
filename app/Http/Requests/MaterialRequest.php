<?php

namespace App\Http\Requests;

use App\Rules\NotLowercase;
use App\Rules\NotUppercase;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', new NotUppercase, new NotLowercase, 'max:100'],
            'material_types' => ['required','max:20'],
            'unit' => ['required', 'max:100'],
            'cost' => ['required','max:10'],
            'stock' => ['required', 'max:100'],
            'expiration_days' => ['max:10'],
        ];
    }
}
