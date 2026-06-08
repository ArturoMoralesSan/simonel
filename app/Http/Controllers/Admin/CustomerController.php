<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserCustomerRequest;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Hash;
use Auth;
use Illuminate\Support\Collection;

class CustomerController extends Controller
{
    public function index()
    {
       abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        $users = Customer::with('user')->get();

        return view('admin.clientes.index', compact('users'));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);


        return view('admin.clientes.crear');
    }


    public function save(StoreUserCustomerRequest  $request)
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        if ($request->customer_id == null) {
            $user  = new User;
        } else {
            $user = User::find($request->user_id);
        }
        $user->name    = $request->business_name;
        $user->email   = $request->email;
        $user->role_id = 2;
        $user->save();

        if ($request->customer_id == null) {
            $customer = new Customer;
        } else {
            $customer = Customer::find($request->customer_id);
        }
        $customer->user_id       = $user->id;
        $customer->business_name = $request->business_name;
        $customer->rfc           = $request->rfc;
        $customer->trade_name    = $request->trade_name;
        $customer->tax_regime    = $request->tax_regime;
        $customer->contact       = $request->contact;
        $customer->phone         = $request->phone;
        $customer->email         = $request->email;
        $customer->street          = $request->street;
        $customer->ext_number      = $request->ext_number;
        $customer->int_number      = $request->int_number;
        $customer->between_streets = $request->between_streets;
        $customer->and_street      = $request->and_street;
        $customer->country         = $request->country ?? 'MEX. México';
        $customer->state           = $request->state;
        $customer->municipality    = $request->municipality;
        $customer->population      = $request->population;
        $customer->colony          = $request->colony;
        $customer->postal_code     = $request->postal_code;
        $customer->save();

        if ($request->customer_id == null) {
            alert('Se ha agregado un cliente.');
        } else {
            alert('Se ha editado un cliente.');
        }

        return response('', 204, [
            'Redirect-To' => url('admin/clientes/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        $user  = Customer::find($id);

        return view('admin.clientes.editar', compact('user'));
    }

    public function destroy($id)
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        if (Auth::user()->id !== $id) {
            $user = User::find($id);
            $user->delete();
            alert('Se ha eliminado un cliente.');
        }

        alert('No se ha podido eliminar un cliente.');
        return response('', 204);

    }
}
