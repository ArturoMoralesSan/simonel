<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountReceivable;
use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreAccountReceivablePaymentRequest;
use App\Models\AccountReceivablePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountsReceivableController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('view.accountsreceivable'), 403);

        $query = AccountReceivable::with(['customer', 'sale', 'seller']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where(
                    'business_name',
                    'LIKE',
                    '%' . $request->customer . '%'
                );
            });
        }

        $accounts = $query->orderBy('due_date')->paginate(10)->appends(request()->all());

        $accountsItems = collect($accounts->items());

        $links = $accounts->links('layout.pagination');

        $summary = [
            'pending' => AccountReceivable::whereIn('status', ['Pendiente', 'Parcial'])->sum('balance'),

            'overdue' => AccountReceivable::whereDate('due_date', '<', today())
                ->whereIn('status', ['Pendiente', 'Parcial'])
                ->sum('balance'),

            'customers' => AccountReceivable::whereIn('status', ['Pendiente', 'Parcial'])
                ->distinct('customer_id')
                ->count('customer_id'),

            'documents' => AccountReceivable::whereIn('status', ['Pendiente', 'Parcial'])
                ->count(),
        ];

        return view(
            'admin.cobranza.index',
            compact(
                'accountsItems',
                'links',
                'summary'
            )
        );
    }

    public function details($id)
    {
        abort_unless(Gate::allows('view.accountsreceivable'), 403);

        $account = AccountReceivable::with([
            'customer',
            'sale.products.product.manufactured',
            'seller',
            'payments'
        ])->findOrFail($id);


        return view('admin.cobranza.details',compact('account'));
    }

    public function payment($id)
    {
        abort_unless(Gate::allows('create.accountsreceivable'), 403);

        $account = AccountReceivable::with('customer')->findOrFail($id);

        $payments = Payment::where('key_name', '!=', 'credito-simonel')->pluck('name','id');

        return view(
            'admin.cobranza.pagos', compact('account', 'payments')
        );
    }

    public function save(StoreAccountReceivablePaymentRequest $request, $id)
    {
        abort_unless(Gate::allows('create.accountsreceivable'), 403);

        DB::beginTransaction();

        try {

            $account = AccountReceivable::findOrFail($id);

            if($request->amount > $account->balance){
                throw new \Exception('El pago no puede superar el saldo pendiente.');
            }

            AccountReceivablePayment::create([
                'account_receivable_id' => $account->id,
                'payment_id' => $request->payment_id,
                'amount' => $request->amount,
                'payment_date' => Carbon::now(),
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);

            $account->paid_amount += $request->amount;
            $account->balance -= $request->amount;

            if($account->balance <= 0){
                $account->balance = 0;
                $account->status = 'Pagada';
            }
            elseif($account->paid_amount > 0){
                $account->status = 'Parcial';
            }

            $account->save();

            DB::commit();

            alert('Pago registrado correctamente.');

        } catch(\Throwable $e){
            DB::rollBack();
            alert(
                $e->getMessage(),
                'danger'
            );
        }

        return response('',204,['Redirect-To'=>url('admin/cuentas-pendientes/'.$id)]);

    }
}
