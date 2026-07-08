<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\Type;
use App\Models\Cut;
use App\Models\SaleProduct;
use App\Models\Product;
use App\Models\Payment;
use App\Models\ProductLot;
use App\Models\SaleProductLot;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;
use App\Mail\SaleMail;
use Illuminate\Support\Facades\Mail;
use Luecano\NumeroALetras\NumeroALetras;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleController extends Controller
{
    public function index()
    {
        abort_unless(
            Gate::allows('view.quotations') || Gate::allows('create.quotations'),
            403
        );

        $actual_day = Carbon::now()->day;
        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $search = request('search');
        $day    = request('day', $actual_day);
        $month  = request('month', $actual_month);
        $year   = request('year', $actual_year);

        $years = collect();
        for ($año = 2026; $año <= $actual_year; $año++) {
            $years[$año] = $año;
        }

        $months = collect([
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ]);

        $days = collect(range(1, 31))->mapWithKeys(function ($day) {
            return [$day => $day];
        });

        $query = Sale::with('products', 'user')
        ->whereDay('created_at', $day)
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->latest()

        ->when(!Auth::user()->isSuperAdmin(), function ($query) {

            if (Auth::user()->isCustomer()) {

                $query->where('user_id', Auth::id());

            } else {

                $query->whereHas('user.customer', function ($q) {
                    $q->where('seller_id', Auth::id());
                });

            }

        });

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            });
        }

        $sales = $query->paginate(20)->appends(request()->query());

        $saleItems = collect($sales->items());
        $links = $sales->links('layout.pagination');

        return view('admin.ventas.index', compact(
            'years',
            'months',
            'days',
            'actual_day',
            'actual_month',
            'actual_year',
            'saleItems',
            'links'
        ));
    }


    public function create()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        if (Auth::user()->isCustomer()) {

            $users = Customer::where('user_id', Auth::id())
                ->selectRaw("
                    CONCAT(trade_name,' (',business_name,')') as full_name,
                    user_id
                ")
                ->pluck('full_name', 'user_id');

        } elseif (Auth::user()->isSuperAdmin()) {

            $users = Customer::selectRaw("
                    CONCAT(trade_name,' (',business_name,')') as full_name,
                    user_id
                ")
                ->orderBy('trade_name')
                ->pluck('full_name', 'user_id');

        } else {

            // Vendedor
            $users = Customer::where('seller_id', Auth::id())
                ->selectRaw("
                    CONCAT(trade_name,' (',business_name,')') as full_name,
                    user_id
                ")
                ->orderBy('trade_name')
                ->pluck('full_name', 'user_id');
        }


        $payments = Payment::pluck('name','id');

        $products = Product::select('products.*')
        ->leftJoin('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
        ->with(['manufactured'])
        ->orderBy('manufactured_products.name')
        ->get();

        return view('admin.ventas.crear',compact('users','products','payments'));
    }

    public function order($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        if (Auth::user()->isCustomer()) {
            return redirect('admin/ventas');
        }

        $sale = Sale::with([
            'products.product.manufactured',
            'user',
            'payments'
        ])->findOrFail($id);

        $status = collect([
            'accepted' => 'Aceptada',
            'paid'     => 'Pagada',
            'assortment' => 'Surtida',
            'credit' => 'Crédito',

        ]);

        $paid = collect([
            '1' => 'Pagado',
            '0' => 'No pagado',
        ]);

        $payments = Payment::pluck('name','id');

        return view('admin.ventas.orden', compact('sale', 'status', 'paid', 'payments'));
        
    }

    /* public function save(SaleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        dd($request);
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            if (!$request->sale_id) {
                $sale = new Sale;
            } else {
                $sale = Sale::findOrFail($request->sale_id);
            }

            $sale->user_id = $validated['client_id'];
            $sale->comment = $validated['comment'] ?? null;
            $sale->status = 'accepted';
            $sale->save();

            SaleProduct::where('sale_id',$sale->id)->delete();

            $subtotal = 0;
            $ivaTotal = 0;

            foreach ($validated['products'] as $product) {

                $quantity = (float) ($product['quantity'] ?? 1);
                $unitPrice = (float) ($product['unit_price'] ?? 0);
                $discount = (float) ($product['discount'] ?? 0);
                $iva = (float) ($product['iva'] ?? 0);

                $base = $quantity * $unitPrice;
                $discounted =$base - ($base * $discount / 100);
                $ivaAmount = $discounted * $iva / 100;

                $subtotal += $discounted;
                $ivaTotal += $ivaAmount;

                $saleProduct = new SaleProduct;
                $saleProduct->sale_id = $sale->id;
                $saleProduct->product_id = $product['product_id'];
                $saleProduct->quantity = $quantity;
                $saleProduct->base_price = $unitPrice;
                $saleProduct->discount = $discount;
                $saleProduct->iva = $iva;
                $saleProduct->subtotal = $discounted;
                $saleProduct->total_with_iva = $discounted + $ivaAmount;
                $saleProduct->save();
            }

            $total = $subtotal + $ivaTotal;

            $sale->gross_amount = $request->gross_amount;
            $sale->discount = $request->discounts;
            $sale->total_sale_price = $subtotal;
            $sale->iva = $ivaTotal;
            $sale->total_with_iva = $total;

            $formatter = new NumeroALetras();
            $formatter->conector = 'Y';
            $sale->letter = $formatter->toMoney($total, 2, 'pesos', 'centavos');
            $sale->save();

            $this->handleAcceptedSale($sale, $request);

            DB::commit();

            alert(
                !$request->sale_id ? 'Se ha creado la solicitud.' : 'Se ha actualizado la solicitud.'
            );

            return response('', 204, [
                'Redirect-To' => url('admin/ventas')
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            alert(
                $e->getMessage(),
                'danger'
            );

            return response('', 204, [
                'Redirect-To' => url('admin/ventas')
            ]);
        }
    } */

    public function save(SaleRequest $request)
    {
        abort_unless(
            Gate::allows('view.quotations') || Gate::allows('create.quotations'),
            403
        );

        $validated = $request->validated();

        DB::beginTransaction();

        try {

            if (!$request->sale_id) {
                $sale = new Sale;
            } else {
                $sale = Sale::findOrFail($request->sale_id);
            }

            $sale->user_id = $validated['client_id'];
            $sale->comment = $validated['comment'] ?? null;
            $sale->save();

            $totals = $this->saveProducts($sale, $validated);

            $sale->gross_amount = $request->gross_amount;
            $sale->discount = $request->discounts;
            $sale->total_sale_price = $totals['subtotal'];
            $sale->iva = $totals['iva'];
            $sale->total_with_iva = $totals['total'];

            $formatter = new NumeroALetras();
            $formatter->conector = 'Y';

            $sale->letter = $formatter->toMoney(
                $totals['total'],
                2,
                'pesos',
                'centavos'
            );

            $sale->status = $this->savePayments($sale, $request);

            $sale->save();

            $this->handleSale($sale);

            DB::commit();

            alert(
                !$request->sale_id
                    ? 'Se ha creado la venta.'
                    : 'Se ha actualizado la venta.'
            );

            return response('',204,[
                'Redirect-To'=>url('admin/ventas')
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            alert($e->getMessage(),'danger');

            return response('',204,[
                'Redirect-To'=>url('admin/ventas')
            ]);
        }
    }

    private function saveProducts(Sale $sale, array $validated)
    {
        SaleProduct::where('sale_id',$sale->id)->delete();

        $subtotal = 0;
        $ivaTotal = 0;

        foreach ($validated['products'] as $product) {

            $quantity = (float)$product['quantity'];
            $unitPrice = (float)$product['unit_price'];
            $discount = (float)($product['discount'] ?? 0);
            $iva = (float)($product['iva'] ?? 0);

            $base = $quantity * $unitPrice;

            $discounted = $base - ($base * $discount / 100);

            $ivaAmount = $discounted * $iva / 100;

            $subtotal += $discounted;
            $ivaTotal += $ivaAmount;

            SaleProduct::create([
                'sale_id'        => $sale->id,
                'product_id'     => $product['product_id'],
                'quantity'       => $quantity,
                'base_price'     => $unitPrice,
                'discount'       => $discount,
                'iva'            => $iva,
                'subtotal'       => $discounted,
                'total_with_iva' => $discounted + $ivaAmount,
            ]);
        }

        return [
            'subtotal'=>$subtotal,
            'iva'=>$ivaTotal,
            'total'=>$subtotal + $ivaTotal
        ];
    }

    private function savePayments(Sale $sale, Request $request)
    {
        $status = 'paid';

        $sale->payments()->detach();

        for ($i = 1; $i <= $request->payments_count; $i++) {

            if (!$request->input("payment{$i}_pago")) {
                continue;
            }

            $paymentId = $request->input("payment{$i}_pago");

            $sale->payments()->attach(
                $paymentId,
                [
                    'cost' => $request->input("payment{$i}_cost", 0)
                ]
            );

            // Crédito Simonel
            if ($paymentId == 9) {
                $status = 'credit';
            }
        }

        if ($status == 'paid') {
            $sale->is_paid = 1;
            $sale->finish_date = now()->format('Y-m-d');
        } else {
            $sale->is_paid = 0;
            $sale->finish_date = null;
        }

        $sale->status = $status;

        return $status;
    }

    private function handleSale(Sale $sale)
    {
        foreach ($sale->products as $saleProduct) {

            $remaining = $saleProduct->quantity;

            $lots = ProductLot::where('product_id',$saleProduct->product_id)
                ->where('available_quantity','>',0)
                ->where('status','Disponible')
                ->orderBy('production_date')
                ->orderBy('id')
                ->get();

            $available = $lots->sum('available_quantity');

            if ($available < $remaining) {

                throw new \Exception(
                    'No hay existencia suficiente de '
                    .$saleProduct->product->name.
                    '. Disponible: '.number_format($available,3).
                    ', Solicitado: '.number_format($remaining,3)
                );
            }

            foreach ($lots as $lot) {

                if ($remaining <= 0) {
                    break;
                }

                $consume = min($remaining,$lot->available_quantity);

                SaleProductLot::create([
                    'sale_product_id'=>$saleProduct->id,
                    'product_lot_id'=>$lot->id,
                    'quantity'=>$consume,
                ]);

                $lot->available_quantity -= $consume;
                $lot->total_cost = $lot->available_quantity * $lot->cost_per_unit;

                if ($lot->available_quantity <= 0) {
                    $lot->available_quantity = 0;
                    $lot->status = 'Agotado';
                }

                $lot->save();

                $remaining -= $consume;
            }
        }
    }

    

    public function orderupdate(OrderRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        DB::beginTransaction();

        try {

            $sale = Sale::with(['products.product', 'user', 'payments'])->findOrFail($id);

            if ($request->status == 'accepted' && $sale->status != 'accepted') {

                $sale->status = 'accepted';
                $sale->comment = $request->comment;

                foreach ($sale->products as $saleProduct)
                {
                    $remaining = $saleProduct->quantity;

                    $lots = ProductLot::where('product_id', $saleProduct->product_id)
                        ->where('available_quantity', '>', 0)
                        ->where('status', 'Disponible')
                        ->orderBy('production_date')
                        ->orderBy('id')
                        ->get();

                    $available = $lots->sum('available_quantity');

                    if ($available < $remaining) {

                        throw new \Exception('No hay existencia suficiente de ' .
                            $saleProduct->product->name .
                            '. Disponible: ' .
                            number_format($available,3) .
                            ', Solicitado: ' .
                            number_format($remaining,3)
                        );
                    }

                    foreach ($lots as $lot) {

                        if ($remaining <= 0) {
                            break;
                        }

                        $consume = min($remaining, $lot->available_quantity);

                        SaleProductLot::create([
                            'sale_product_id' => $saleProduct->id,
                            'product_lot_id'  => $lot->id,
                            'quantity'        => $consume,
                        ]);

                        $lot->available_quantity -= $consume;

                        $lot->total_cost = $lot->available_quantity * $lot->cost_per_unit;

                        if ($lot->available_quantity <= 0) {

                            $lot->available_quantity = 0;
                            $lot->status = 'Agotado';
                        }

                        $lot->save();

                        $remaining -= $consume;
                    }
                }
            }

            if ($request->status == 'paid') {

                $sale->status = 'paid';
                $sale->is_paid = 1;
                $sale->finish_date = now()->format('Y-m-d');
            }

            $sale->save();

            $sale->payments()->detach();

            for ($i = 1;$i <= $request->payments_count;$i++) {

                if (!$request->input('payment'.$i.'_pago')) {
                    continue;
                }

                $sale->payments()->attach(
                    $request->input('payment'.$i.'_pago'),
                    [
                        'cost' => $request->input('payment'.$i.'_cost', 0)
                    ]
                );
            }

            DB::commit();

            alert('Se ha actualizado la orden.');

            return response('', 204, [
                'Redirect-To' => url('admin/ventas')
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            alert($e->getMessage(), 'danger');

            return response('', 204, [
                'Redirect-To' => url('admin/ventas')
            ]);
        }
    }
    

    /* private function handleAcceptedSale(Sale $sale, $request)
    {
        if ($sale->status !== 'accepted') {
            return;
        }

        $sale->comment = $request->comment;

        foreach ($sale->products as $saleProduct) {

            $remaining = $saleProduct->quantity;

            $lots = ProductLot::where('product_id', $saleProduct->product_id)
                ->where('available_quantity', '>', 0)
                ->where('status', 'Disponible')
                ->orderBy('production_date')
                ->orderBy('id')
                ->get();

            $available = $lots->sum('available_quantity');

            if ($available < $remaining) {
                throw new \Exception(
                    'No hay existencia suficiente de ' .
                    $saleProduct->product->name .
                    '. Disponible: ' .
                    number_format($available, 3) .
                    ', Solicitado: ' .
                    number_format($remaining, 3)
                );
            }

            foreach ($lots as $lot) {

                if ($remaining <= 0) {
                    break;
                }

                $consume = min($remaining, $lot->available_quantity);

                SaleProductLot::create([
                    'sale_product_id' => $saleProduct->id,
                    'product_lot_id' => $lot->id,
                    'quantity' => $consume,
                ]);

                $lot->available_quantity -= $consume;
                $lot->total_cost = $lot->available_quantity * $lot->cost_per_unit;

                if ($lot->available_quantity <= 0) {
                    $lot->available_quantity = 0;
                    $lot->status = 'Agotado';
                }

                $lot->save();

                $remaining -= $consume;
            }
        }

        $sale->save();
    } */

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $sale = Sale::with(['products', 'user'])->findOrFail($id);

        if ($sale->status != 'quoted' || (Auth::user()->isCustomer() && $sale->user_id != Auth::id())) {
            return redirect('admin/ventas');
        }

        if (Auth::user()->isCustomer()) {

            $users = Customer::where('user_id', Auth::id())
                ->selectRaw(" CONCAT(trade_name,' (', business_name, ')') as full_name,  user_id")
                ->pluck('full_name', 'user_id');

        } else {

            $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name, ')' ) as full_name, user_id")
                ->pluck('full_name', 'user_id');
        }

        $products = Product::select('products.*')
        ->leftJoin('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
        ->orderBy('manufactured_products.name')
        ->with('manufactured')
        ->get();
        
        return view('admin.ventas.editar', compact('sale', 'users', 'products')
        );
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $quote = Sale::find($id);
        $quote->delete();
        
        return response('', 204);

    }

    public function sendSaleMail($sale)
    {
        $pdf = Pdf::loadView('admin.pdf.notesale', compact('sale'));
        $pdfPath = storage_path("app/public/cotizacion_{$sale->id}.pdf");
        $pdf->save($pdfPath);

        $admins = User::where('role_id', '1')->pluck('email')->toArray();

        if (!empty($sale->user->email)) {
            Mail::to($sale->user->email)
                ->bcc($admins)
                ->send(new SaleMail($sale, $pdfPath));
        } else {
            Mail::bcc($admins)
            ->send(new SaleMail($sale, $pdfPath));
        }

        return back()->with('success', 'Correo enviado con éxito.');
    }
}
