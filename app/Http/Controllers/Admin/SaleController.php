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
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $search = \Request('search');
        $month = \Request('month') ?? $actual_month;
        $year  = \Request('year') ?? $actual_year;
        
        $years = collect([]);
        for ($año = 2025; $año <= $actual_year; $año++) {
            $years[$año] = $año;
        }
        $months = collect([
            '1' => 'Enero', 
            '2' => 'Febrero', 
            '3' => 'Marzo', 
            '4' => 'Abril',
            '5' => 'Mayo', 
            '6' => 'Junio', 
            '7' => 'Julio', 
            '8' => 'Agosto',
            '9' => 'Septiembre', 
            '10' => 'Octubre', 
            '11' => 'Noviembre', 
            '12' => 'Diciembre'
        ]);
        

        $statusLabels = [
            'quoted'   => 'Cotizaciones',
            'ordered'  => 'Ordenes pendientes',
            'accepted' => 'Ordenes aceptadas',
            'paid'     => 'Ordenes pagadas',
        ];

        $salesByStatus = collect([]);
        foreach ($statusLabels as $status => $label) {
            $query = Sale::with('products', 'user')
                ->where('status', $status)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'DESC');

            if (Auth::user()->isCustomer()) {
                $query->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc');
            }

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where(function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    });
                });
            }

            $paginatedSales = $query->paginate(10)->appends(request()->all());
            $salesByStatus[$status] = [
                'items' => collect($paginatedSales->items()),
                'links' => $paginatedSales->links('layout.pagination') 
            ];
        }

        return view('admin.ventas.index',compact('years', 'months', 'actual_month', 'actual_year', 'salesByStatus','statusLabels'));
    }

    public function order($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        if (Auth::user()->isCustomer()) {
            return redirect('admin/ventas');
        }

        $sale = Sale::with(['products.product.type', 'user','payments'])->findOrFail($id);

        $status = collect([
            'quoted'   => 'Cotizada',
            'ordered'  => 'Ordenada',
            'accepted' => 'Aceptada',
            'paid'     => 'Pagada',
        ]);

        $paid = collect([
            '1' => 'Pagado',
            '0' => 'No pagado',
        ]);

        $payments = Payment::pluck('name','id');

        return view('admin.ventas.orden', compact('sale', 'status', 'paid', 'payments'));
        
    }

    public function orderupdate(OrderRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        DB::beginTransaction();

        try {

            $sale = Sale::with(['products.product', 'user', 'payments'])->findOrFail($id);

            if (
                $request->status == 'ordered' && $sale->status == 'quoted') {
                $sale->status = 'ordered';
                $sale->comment = $request->comment;
                //$this->sendSaleMail($sale);
            }

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


    public function cloneSale($id)
    {
        $originalSale = Sale::with('templates')->findOrFail($id);

        DB::beginTransaction();

        try {
            // Clonar la venta
            $newSale = $originalSale->replicate();
            $newSale->total_sale_price = 0;
            $newSale->iva = 0;
            $newSale->total_with_iva = 0;
            $newSale->letter = null;
            $newSale->save();

            $subtotal = 0;
            $ivaTotal = 0;

            // Clonar los productos/templates
            foreach ($originalSale->templates as $template) {
                $newSale->templates()->attach($template->id, [
                    'product_name' => $template->pivot->product_name,
                    'quantity' => $template->pivot->quantity,
                    'base_price' => $template->pivot->base_price,
                    'discount' => $template->pivot->discount,
                    'iva' => $template->pivot->iva,
                    'subtotal' => $template->pivot->subtotal,
                    'total_with_iva' => $template->pivot->total_with_iva,
                ]);
            }

            $newSale->total_sale_price = $originalSale->total_sale_price;
            $newSale->gross_amount = $originalSale->gross_amount;
            $newSale->discount = $originalSale->discount;
            $newSale->iva = $originalSale->iva;
            $newSale->total_with_iva = $originalSale->total_with_iva;

            // Convertir total a letras
            $formatter = new NumeroALetras();
            $formatter->conector = 'Y';
            $newSale->letter = $formatter->toMoney($originalSale->total_with_iva, 2, 'pesos', 'centavos');

            $newSale->save();

            DB::commit();

            $newSale->load('templates', 'user');

            return response()->json([
                'success' => true,
                'newItem' => $newSale
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Ocurrió un error al clonar la cotización',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        if (Auth::user()->isCustomer()) {

            $users = Customer::where('user_id', Auth::user()->id)
                ->selectRaw("CONCAT(trade_name,' (',business_name,')') as full_name, user_id")
                ->pluck('full_name', 'user_id');

        } else {

            $users = Customer::selectRaw("CONCAT(trade_name,' (',business_name,')') as full_name, user_id")
                ->pluck('full_name','user_id');
        }

        $products = Product::with(['type'])->orderBy('name')->get();

        return view('admin.ventas.crear',compact('users','products'));
    }

    public function save(SaleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

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

            DB::commit();

            alert(
                !$request->sale_id ? 'Se ha creado la cotización.' : 'Se ha actualizado la cotización.'
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
    }

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

        $products = Product::orderBy('name')->get();

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
