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
use App\Models\ProductTemplate;
use App\Models\Product;
use App\Models\Payment;
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
            $query = Sale::with('templates', 'user')
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
        
        $sale = Sale::with(['templates.product.type', 'user'])->find($id);
        if (Auth::user()->isCustomer()) 
        {
            return redirect('admin/ventas/');
        }


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
        $assigned_payments = $sale->payments()->get();
        $payments = Payment::pluck('name','id');      

        return view('admin.ventas.orden', compact('sale','status', 'paid', 'payments','assigned_payments'));
    }
    public function orderupdate(OrderRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $sale = Sale::find($id);


        if($request->status == 'ordered' && $sale->status == 'quoted')
        { 
            foreach($sale->products as $product)
            {
                $inventory = Inventory::with('product')->where('product_id', $product->id)->first();
                $updateQuantity = $inventory->quantity - $product->pivot->quantity_product;
                $inventory->quantity = $updateQuantity;
                $inventory->total = $inventory->product->costo_venta * $updateQuantity;
                $inventory->save();
                Inventory::checkStock($inventory);
            }
            
            $sale->status = $request->status;
            $sale->comment = $request->comment;

            $sale->load('products', 'user');

            $this->sendSaleMail($sale);
        }
        
        if($request->status == 'paid')
        {
            $dateNow     = Carbon::now();
            $dateFormat  = $dateNow->format('Y-m-d');

            $sale->is_paid = true;
            $sale->finish_date = $dateFormat;

        }
        $sale->save();
        $sale->payments()->detach();
        for($i = 1; $i <= $request->payments_count; $i++){
            $sale->payments()->attach($request['payment'.$i.'_pago'], ['cost'=> $request['payment'.$i.'_cost']]);
        }

        alert('Se ha actualizado una orden.');

        return response('', 204, [
            'Redirect-To' => url('admin/ventas/')
        ]);
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
            ->selectRaw("CONCAT(trade_name, ' (',business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');
        } else {
            $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');       
        }        
        $templates = ProductTemplate::with('product')->get();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();

        return view('admin.ventas.crear', compact('users', 'templates', 'types', 'cuts'));   
    }

    public function save(SaleRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $validated = $request->validated();
        DB::beginTransaction();

        try {

           if($request->sale_id == null)
                $sale = new Sale;
            else {
                $sale = Sale::find($request->sale_id);
            }

            $sale->user_id = $validated['client_id'];
            $sale->comment = $validated['comment'] ?? null;
            $sale->save();

            $sale->templates()->detach();

            $subtotal = 0;
            $ivaTotal = 0;

            foreach ($validated['products'] as $product) {
                $quantity = (int) ($product['quantity'] ?? 1);
                $unitPrice = (float) ($product['unit_price'] ?? 0);
                $discount = (float) ($product['discount'] ?? 0);
                $iva = (float) ($product['iva'] ?? 0);

                $base = $quantity * $unitPrice;
                $discounted = $base - ($base * $discount / 100);
                $ivaAmount = $discounted * $iva / 100;

                $subtotal += $discounted;
                $ivaTotal += $ivaAmount;

                $sale->templates()->attach($product['template_id'], [
                    'product_name' => $product['custom_name'] ?? null,
                    'quantity'  => $product['quantity'],
                    'base_price'  => $product['unit_price'],
                    'discount' => $discount,
                    'iva' => $iva,
                    'subtotal' => $discounted,
                    'total_with_iva' => $discounted + $ivaAmount,
                ]);

                $template = ProductTemplate::find($product['template_id']);

                if ($template && $template->has_inventory) {
                    $inventory = Inventory::where('template_id', $template->id)->where('user_id', $validated['client_id'])->first();
                    $inventory->quantity -= $quantity;
                    $inventory->save();

                    InventoryMovement::create([
                        'date' => Carbon::now(),
                        'inventory_id' => $inventory->id,
                        'template_id' => $template->id,
                        'sale_id' => $sale->id,
                        'type' => 'salida',
                        'quantity' => $quantity,
                        'base_price' => $unitPrice,
                    ]);
                }
            }

            $total = $subtotal + $ivaTotal;
            $sale->gross_amount = $request->gross_amount;
            $sale->total_sale_price = $subtotal;
            $sale->discount = $request->discounts;
            $sale->iva  = $ivaTotal;
            $sale->total_with_iva  = $total;

            // convertir total a letras
            $formatter = new NumeroALetras();
            $formatter->conector = 'Y';
            $sale->letter = $formatter->toMoney($total, 2, 'pesos', 'centavos');

            $sale->save();

            DB::commit();

            alert($request->sale_id == null
                ? 'Se ha agregado un elemento.'
                : 'Se ha editado un elemento.'
            );

            return response('', 204, [
                'Redirect-To' => url('admin/ventas/')
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Ocurrió un error al guardar la cotización',
                'error'   => $e->getMessage(),
            ], 500);
        }
        
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $sale = Sale::with('templates')->find($id);

        if ($sale->status != 'quoted' || (Auth::user()->isCustomer() && $sale->user_id != Auth::user()->id)) 
        {
            return redirect('admin/ventas/');
        }
        
        if (Auth::user()->isCustomer()) {
            $users = Customer::where('user_id', Auth::user()->id)
            ->selectRaw("CONCAT(trade_name, ' (',business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');
        } else {
            $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');       
        }           
        $templates = ProductTemplate::with('product')->get();

        $assigned_products = $sale->templates()->get();

        return view('admin.ventas.editar', compact('sale','users', 'templates', 'assigned_products'));
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
