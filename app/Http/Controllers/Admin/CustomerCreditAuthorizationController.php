<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCreditAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomerCreditAuthorizationController extends Controller
{

    public function store(Request $request)
    {
        abort_unless(Gate::allows('view.customers') ||  Gate::allows('create.customers'), 403);


        $request->validate([
            'customer_id' => ['required','exists:customers,id'],
            'reason' => ['required','string','max:2000'],
            'expires_at' => ['required','date'],
        ]);


        $authorization = new CustomerCreditAuthorization();
        $authorization->customer_id = $request->customer_id;
        $authorization->authorized_by = Auth::id();
        $authorization->reason = $request->reason;
        $authorization->expires_at = $request->expires_at;
        $authorization->save();


        alert('Se ha autorizado el crédito del cliente.');


        return response('',204,[
            'Redirect-To'=>url(
                'admin/clientes/'.$request->customer_id.'/detalles'
            )
        ]);

    }


    public function show($customer_id)
    {

        $customer = Customer::with([
            'creditAuthorizations.authorizer'
        ])
        ->findOrFail($customer_id);


        return view(
            'admin.clientes.credit-authorizations',
            compact('customer')
        );

    }

}