<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.payments') || Gate::allows('create.payments'), 403);
        $payments = Payment::all();
        return view('admin.pagos.index', compact('payments'));   
    }

    public function save(PaymentRequest $request)
    {
        abort_unless(Gate::allows('view.payments') || Gate::allows('edit.payments'), 403);
        
        $payment = new Payment;
        $payment->name = $request->name;
        $payment->key_name = Str::slug($request->name);
        $payment->save();

        alert('Se ha agregado un tipo de pago.');

        return response('', 204, [
            'Redirect-To' => url('admin/tipos-pagos/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.payments') || Gate::allows('edit.payments'), 403);
        $payment = Payment::find($id);
        return view('admin.pagos.editar', compact('payment'));
    }


    public function update(PaymentRequest $request, $id)
    {
        abort_unless(Gate::allows('view.payments') || Gate::allows('edit.payments'), 403);

        $payment = Payment::find($id);
        $payment->name = $request->name;
        $payment->key_name = Str::slug($request->name);
        $payment->save();

        alert('Se ha actualizado un tipo de pago.');

        return response('', 204, [
            'Redirect-To' => url('admin/tipos-pagos/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.payments') || Gate::allows('create.payments'), 403);

        $payment = Payment::find($id);
        $payment->delete();

        return response('', 204);

    }
}
