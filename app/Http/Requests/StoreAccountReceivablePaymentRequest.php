<?php

namespace App\Http\Requests;
use App\Models\AccountReceivable;

class StoreAccountReceivablePaymentRequest extends FormRequest
{
    
    public function rules()
    {
        return [

            'payment_id' => ['required', 'exists:payments,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'max:500'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $account = AccountReceivable::find($this->route('id'));

            if (!$account) {
                return;
            }

            if ($this->amount > $account->balance) {
                $validator->errors()->add(
                    'amount',
                    'El monto del pago no puede ser mayor al saldo pendiente de la cuenta.'
                );

            }

        });
    }
}
