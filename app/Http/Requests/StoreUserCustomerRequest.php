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
            'phone'           => 'required|string|max:20',
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

            // Crédito
        ];

        // Validación de RFC y correo
        if ($this->customer_id === null) {
            $rules['rfc'] .= '|unique:customers,rfc';
            $rules['email'] .= '|unique:users,email';
        } else {
            $rules['rfc'] .= '|unique:customers,rfc,' . $this->customer_id;
            $rules['email'] .= '|unique:users,email,' . $this->user_id;
        }

        // Solo Admin y SuperAdmin pueden registrar crédito
        $canManageCredit =
            Auth::user()->isSuperAdmin() ||
            Auth::user()->isAdmin();

        // Si tiene crédito y es Admin/SuperAdmin,
        // los datos del crédito son obligatorios.
        if ((int) $this->credit === 1 && $canManageCredit) {
            $rules['credit_limit'] = 'required|numeric|min:1';
            $rules['credit_days']  = 'required|integer|min:1|max:365';
        }

        return $rules;
    }
}