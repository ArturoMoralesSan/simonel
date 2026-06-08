<?php

namespace App\Http\Requests;


class ExpenseRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id'  => 'required|max:20',
            'expense_id' => 'required',
            'cost'       => 'required',
            'name'       => 'required',    
        ];
            
    }
}
