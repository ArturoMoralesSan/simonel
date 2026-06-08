<?php

namespace App\Http\Requests;


class ServiceRequest extends FormRequest
{
    
    public function rules()
    {
        $rules = [
            'patient'   => 'required|string|max:160',
            'rx_prev'   => 'required|max:10',
            'branch_id' => 'required|max:20',
            'print'     => 'required',
            'no_rx'     => 'required_if:print,Sí',
            'study.*._estudio' => 'required|string|max:255',

        ];

        for($i = 1; $i <= $this->studies_count; $i++) {
            $rules['study' . $i . '_estudio'] = 'required';
        }
        for($i = 1; $i <= $this->payments_count; $i++) {
            $rules['payment' . $i . '_estudio'] = 'required';
            $rules['payment' . $i . '_cost'] = 'required';
        }
        
        return $rules;

    }
}
