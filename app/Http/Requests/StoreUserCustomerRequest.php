<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [

            'business_name'   => 'required|string|max:255',
            'email'           => 'nullable|email',
            'rfc'             => 'nullable|string|max:13|min:12',
            'trade_name'      => 'nullable|string|max:255',
            'tax_regime'      => 'nullable|string|max:255',
            'customer_type'   => 'nullable|string|max:255',
            'phone'           => 'required|digits:10',

            'street'          => 'nullable|string|max:255',
            'ext_number'      => 'nullable|string|max:20',
            'int_number'      => 'nullable|string|max:20',
            'between_streets' => 'nullable|string|max:255',
            'and_street'      => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',
            'state'           => 'nullable|string|max:255',
            'municipality'    => 'nullable|string|max:255',
            'population'      => 'nullable|string|max:255',
            'colony'          => 'nullable|string|max:255',
            'postal_code'     => 'nullable|string|max:10',

            /*
            |--------------------------------------------------------------------------
            | Precios preferenciales
            |--------------------------------------------------------------------------
            */

            'item_count' => 'nullable|integer|min:0',

            /*
            |--------------------------------------------------------------------------
            | Crédito
            |--------------------------------------------------------------------------
            */
        ];

        /*
        |--------------------------------------------------------------------------
        | Validación de RFC, nombre comercial y correo
        |--------------------------------------------------------------------------
        */

        if ($this->customer_id === null) {

            $rules['trade_name'] .= '|unique:customers,trade_name';
            $rules['email'] .= '|unique:users,email';

        } else {

            $rules['trade_name'] .= '|unique:customers,trade_name,' . $this->customer_id;
            $rules['email'] .= '|unique:users,email,' . $this->user_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Precios preferenciales
        |--------------------------------------------------------------------------
        */

        $priceCount = (int) ($this->price_count ?? 0);

        $rules['price_count'] = 'nullable|integer|min:0';

        for ($i = 1; $i <= $priceCount; $i++) {

            $rules['price' . $i . '_product_id'] =
                'required|integer|exists:products,id';

            $rules['price' . $i . '_price'] =
                'required|numeric|min:0';
        }

        /*
        |--------------------------------------------------------------------------
        | Solo Admin y SuperAdmin pueden registrar crédito
        |--------------------------------------------------------------------------
        */

        $canManageCredit =
            Auth::user()->isSuperAdmin() ||
            Auth::user()->isAdmin();

        /*
        |--------------------------------------------------------------------------
        | Crédito
        |--------------------------------------------------------------------------
        */

        if ((int) $this->credit === 1 && $canManageCredit) {

            $rules['credit_limit'] =
                'required|numeric|min:1';

            $rules['credit_days'] =
                'required|integer|min:1|max:365';
        }

        return $rules;
    }
}