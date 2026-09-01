<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\RawMaterialLot; 
use App\Models\ProductLot;
use App\Models\Warehouse;
use PDF; 

class ReportsController extends Controller
{
    /**
     * Reportes
     */

    public function index(Request $request)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?? $dateFormat;
        $end_date   = $request->end_date ?? $dateFormat;

        $sales = $this->sales($start_date, $end_date)
            ->whereIn('status', [
                'paid',
                'assortment',
                'credit',
            ]);

        $paymentMethods = $sales
            ->flatMap(function ($sale) {
                return $sale->payments;
            })
            ->pluck('name')
            ->map(function ($name) {
                return strtolower(trim($name));
            })
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | ALMACENES
        |--------------------------------------------------------------------------
        */

        $rawMaterialWarehouses = $this->rawMaterialWarehouseResume();

        $productWarehouses = $this->productWarehouseResume();

        return view('admin.reportes.index', [

            'start_date' => $start_date,
            'end_date'   => $end_date,

            /*
            |--------------------------------------------------------------------------
            | VENTAS
            |--------------------------------------------------------------------------
            */

            'summary' => $this->summary($sales),
            'sellers' => $this->sellerResume($sales),
            'products' => $this->productResume($sales),
            'customers' => $this->customerResume($sales),
            'payments' => $this->paymentResume($sales),
            'paymentMethods' => $paymentMethods,

            /*
            |--------------------------------------------------------------------------
            | ALMACENES
            |--------------------------------------------------------------------------
            */

            'rawMaterialWarehouses' => $rawMaterialWarehouses,
            'productWarehouses' => $productWarehouses,

        ]);
    }

    private function rawMaterialWarehouseResume()
    {
        $lots = RawMaterialLot::with([
            'warehouse',
            'material',
        ])
        ->where('available_quantity', '>', 0)
        ->get();

        return $lots
            ->groupBy('warehouse_id')
            ->map(function ($warehouseLots) {

                $warehouse = $warehouseLots->first()->warehouse;

                return (object) [

                    'id' => $warehouse->id ?? null,

                    'name' => $warehouse->name ?? 'Sin almacén',

                    'lots_count' => $warehouseLots->count(),

                    'materials_count' => $warehouseLots
                        ->pluck('raw_material_id')
                        ->unique()
                        ->count(),

                    'quantity' => $warehouseLots
                        ->sum(function ($lot) {
                            return (float) $lot->available_quantity;
                        }),

                    'total' => $warehouseLots
                        ->sum(function ($lot) {

                            return
                                (float) $lot->available_quantity *
                                (float) $lot->cost;
                        }),

                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    private function productWarehouseResume()
    {
        $lots = ProductLot::with([
            'warehouse',
            'product',
        ])
        ->where('available_quantity', '>', 0)
        ->get();

        return $lots
            ->groupBy('warehouse_id')
            ->map(function ($warehouseLots) {

                $warehouse = $warehouseLots->first()->warehouse;

                return (object) [

                    'id' => $warehouse->id ?? null,

                    'name' => $warehouse->name ?? 'Sin almacén',

                    'lots_count' => $warehouseLots->count(),

                    'products_count' => $warehouseLots
                        ->pluck('product_id')
                        ->unique()
                        ->count(),

                    'quantity' => $warehouseLots
                        ->sum(function ($lot) {
                            return (float) $lot->available_quantity;
                        }),

                    'total' => $warehouseLots
                        ->sum(function ($lot) {
                            return (float) $lot->total_cost;
                        }),

                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Resumen General
     */
    private function summary($sales)
    {
        return [
            'totalSales' => $sales->sum('total_with_iva'),

            'salesCount' => $sales->count(),

            'customersCount' => $sales
                ->filter(function ($sale) {
                    return $sale->user &&
                        $sale->user->customer;
                })
                ->pluck('user.customer.id')
                ->unique()
                ->count(),

            'productsSold' => $sales->sum(function ($sale) {
                return $sale->products->sum('quantity');
            }),
        ];
    }

    private function sellerResume($sales)
{
    return $sales
        ->groupBy('seller_id')
        ->map(function ($sellerSales) {

            $seller = $sellerSales->first()->seller;

            $paymentTotals = [];

            foreach ($sellerSales as $sale) {

                foreach ($sale->payments as $payment) {

                    $key = 'payment_' . \Illuminate\Support\Str::slug(
                        strtolower($payment->name),
                        '_'
                    );

                    if (!isset($paymentTotals[$key])) {
                        $paymentTotals[$key] = 0;
                    }

                    $paymentTotals[$key] += $payment->pivot->cost;
                }
            }

            return (object) array_merge([

                'id' => $seller->id,

                'name' => trim(
                    $seller->name . ' ' . $seller->last_name
                ),

                'sales_count' => $sellerSales->count(),

                'total_sales' => $sellerSales->sum('total_with_iva'),

                // Mantener compatibilidad con el PDF actual
                'cash_total' => $paymentTotals['payment_efectivo'] ?? 0,

            ], $paymentTotals);
        })
        ->sortByDesc('total_sales')
        ->values();
}
    /**
     * Resumen por productos
     */
    private function productResume($sales)
    {
        $products = collect();

        foreach ($sales as $sale) {

            foreach ($sale->products as $saleProduct) {

                if (!$saleProduct->product) {
                    continue;
                }

                $id = $saleProduct->product_id;

                if (!$products->has($id)) {

                    $products[$id] = [
                        'id' => $id,
                        'name' => optional($saleProduct->product->manufactured)->name ?? 'Producto eliminado',
                        'sales_count' => 0,
                        'quantity' => 0,
                        'total' => 0,
                    ];

                }

                $item = $products[$id];

                $item['sales_count']++;

                $item['quantity'] += $saleProduct->quantity;

                $item['total'] += $saleProduct->total_with_iva;

                $products[$id] = $item;

            }

        }

        return collect($products)
            ->sortByDesc('total')
            ->values();
    }

    private function customerResume($sales)
    {
        return $sales
            ->groupBy('user_id')
            ->map(function ($customerSales) {

                $customer = $customerSales->first()->user->customer;

                return (object)[
                    'id' => optional($customer)->user_id,
                    'name' => optional($customer)->business_name ?? 'Público General',
                    'sales_count' => $customerSales->count(),
                    'total' => $customerSales->sum('total_with_iva'),
                ];

            })
            ->sortByDesc('total')
            ->values();
    }

    private function paymentResume($sales)
    {
        $payments = collect();

        foreach ($sales as $sale) {

            foreach ($sale->payments as $payment) {

                $id = $payment->id;

                if (!$payments->has($id)) {

                    $payments[$id] = [
                        'id' => $id,
                        'name' => $payment->name,
                        'operations' => 0,
                        'total' => 0,
                    ];

                }

                $item = $payments[$id];

                $item['operations']++;

                $item['total'] += $payment->pivot->cost;

                $payments[$id] = $item;

            }

        }

        return collect($payments)
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Imprimir reporte general
     */
    public function print(Request $request)
    {

    }

    /**
     * Imprimir reporte de todos los vendedores
     */
    public function printSellers(Request $request)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?? $dateFormat;
        $end_date   = $request->end_date ?? $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $sellers = $this->sellerResume($sales);

        $pdf = PDF::loadView('admin.pdf.sellers', [

            'sellers'    => $sellers,
            'start_date' => $start_date,
            'end_date'   => $end_date,

        ]);

        return $pdf->stream(
            "vendedores-{$start_date}-{$end_date}.pdf"
        );
    }

    public function printSeller(Request $request, $id)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?? $dateFormat;
        $end_date = $request->end_date ?? $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $seller = User::findOrFail($id);

        $sellerSales = $sales->where('seller_id', $id);

        $summary = $this->sellerResume($sales)->firstWhere('id', $id);

        $pdf = PDF::loadView('admin.pdf.seller', [
            'seller'     => $seller,
            'sales'      => $sellerSales,
            'summary'    => $summary,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream("vendedor-{$seller->id}.pdf");
    }

    /**
     * Imprimir resumen de todos los productos
     */
    public function printProducts(Request $request)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?: $dateFormat;
        $end_date   = $request->end_date ?: $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $products = $this->productResume($sales);

        $pdf = PDF::loadView('admin.pdf.products', [
            'products'   => $products,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "productos-{$start_date}-{$end_date}.pdf"
        );
    }

    public function printProduct(Request $request, $id)
    {
        $start_date = $request->start_date ?? now()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->format('Y-m-d');

        $product = Product::findOrFail($id);

        $sales = $this->sales($start_date, $end_date);

        /*
        |--------------------------------------------------------------------------
        | Ventas donde aparece el producto
        |--------------------------------------------------------------------------
        */

        $detail = $sales->filter(function ($sale) use ($id) {
            return $sale->products->contains('product_id', $id);
        });


        /*
        |--------------------------------------------------------------------------
        | Resumen del producto
        |--------------------------------------------------------------------------
        */

        $salesCount = 0;
        $quantity = 0;
        $total = 0;


        foreach ($detail as $sale) {

            $item = $sale->products->firstWhere(
                'product_id',
                $id
            );

            if (!$item) {
                continue;
            }


            $itemQuantity = (float) ($item->quantity ?? 0);


            $itemTotal = (float) (
                $item->total_with_iva
                ?? 0
            );


            $salesCount++;

            $quantity += $itemQuantity;

            $total += $itemTotal;
        }


        /*
        |--------------------------------------------------------------------------
        | Resumen
        |--------------------------------------------------------------------------
        */

        $summary = (object) [

            'sales_count' => $salesCount,

            'quantity' => $quantity,

            'total' => $total,

        ];


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = PDF::loadView('admin.pdf.product', [

            'product' => $product,

            'summary' => $summary,

            'sales' => $detail,

            'start_date' => $start_date,

            'end_date' => $end_date,

        ]);


        return $pdf->stream(
            "producto-{$id}.pdf"
        );
    }
    /**
     * PDF - Todos los clientes
     */

    public function printCustomers(Request $request)
    {
        $dateNow = Carbon::now();

        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?: $dateFormat;

        $end_date = $request->end_date ?: $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $customers = $this->customerResume($sales);

        $totalSales = $sales->count();

        $totalAmount = $sales->sum('total_with_iva');

        $pdf = PDF::loadView('admin.pdf.customers', [

            'customers' => $customers,

            'start_date' => $start_date,

            'end_date' => $end_date,

            'totalSales' => $totalSales,

            'totalAmount' => $totalAmount,

        ]);

        return $pdf->stream(
            "clientes-{$start_date}-{$end_date}.pdf"
        );
    }

    public function printCustomer(Request $request, $id)
    {
        $dateNow = Carbon::now();

        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?: $dateFormat;
        $end_date   = $request->end_date ?: $dateFormat;


        /*
        |--------------------------------------------------------------------------
        | Obtener todas las ventas del periodo
        |--------------------------------------------------------------------------
        */

        $allSales = $this->sales($start_date, $end_date);


        /*
        |--------------------------------------------------------------------------
        | Filtrar las ventas del cliente
        |--------------------------------------------------------------------------
        */

        $sales = $allSales
            ->filter(function ($sale) use ($id) {
                return (string) $sale->user_id === (string) $id;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Obtener cliente
        |--------------------------------------------------------------------------
        */

        $customer = optional(
            optional($sales->first())->user
        )->customer;


        /*
        |--------------------------------------------------------------------------
        | Resumen del cliente
        |--------------------------------------------------------------------------
        */

        $summary = (object) [

            'sales_count' => $sales->count(),

            'total' => $sales->sum('total_with_iva'),

        ];


        /*
        |--------------------------------------------------------------------------
        | Generar PDF
        |--------------------------------------------------------------------------
        */

        $pdf = PDF::loadView('admin.pdf.customer', [

            'sales'      => $sales,

            'customer'   => $customer,

            'summary'    => $summary,

            'start_date' => $start_date,

            'end_date'   => $end_date,

        ]);


        return $pdf->stream(
            "cliente-{$id}-{$start_date}-{$end_date}.pdf"
        );
    }



    public function printPayments(Request $request)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?: $dateFormat;
        $end_date   = $request->end_date ?: $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $payments = $this->paymentResume($sales);

        $pdf = PDF::loadView('admin.pdf.payments', [
            'payments'   => $payments,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "metodos-pago-{$start_date}-{$end_date}.pdf"
        );
    }

    /**
     * PDF - Método de pago individual
     */

    public function printPayment(Request $request, $id)
    {
        $dateNow = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = $request->start_date ?: $dateFormat;
        $end_date   = $request->end_date ?: $dateFormat;

        $sales = $this->sales($start_date, $end_date);

        $payment = Payment::findOrFail($id);

        /*
        * Obtener únicamente los movimientos
        * correspondientes a este método de pago.
        */
        $paymentSales = collect();

        foreach ($sales as $sale) {

            foreach ($sale->payments as $salePayment) {

                if ($salePayment->id == $id) {

                    $paymentSales->push((object)[
                        'sale'   => $sale,
                        'amount' => (float) $salePayment->pivot->cost,
                    ]);

                }

            }

        }

        /*
        * Calcular el resumen directamente de las
        * operaciones encontradas.
        */
        $summary = (object)[
            'operations' => $paymentSales->count(),
            'total'      => $paymentSales->sum('amount'),
        ];

        $pdf = PDF::loadView('admin.pdf.payment', [
            'payment'    => $payment,
            'sales'      => $paymentSales,
            'summary'    => $summary,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "metodo-pago-{$payment->id}-{$start_date}-{$end_date}.pdf"
        );
    }


    private function sales($start_date, $end_date)
    {
        return Sale::with([
            'seller',
            'user',
            'customer',
            'products.product.manufactured',
            'payments',
        ])
        ->whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59',
        ])->whereIn('status', [
            'paid',
            'assortment',
            'credit',
        ])
        ->get();
    }

    /**
     * ============================================================
     * PDF - TODOS LOS ALMACENES DE MATERIAS PRIMAS
     * ============================================================
     */
    public function printRawMaterialWarehouses(Request $request)
    {
        $start_date = $request->start_date ?: now()->format('Y-m-d');
        $end_date   = $request->end_date ?: now()->format('Y-m-d');

        $warehouses = $this->rawMaterialWarehouseResume();

        $pdf = PDF::loadView('admin.pdf.raw-material-warehouses', [
            'warehouses' => $warehouses,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "almacenes-materias-primas-{$start_date}-{$end_date}.pdf"
        );
    }


    /**
     * ============================================================
     * PDF - ALMACÉN INDIVIDUAL DE MATERIAS PRIMAS
     * ============================================================
     */
    public function printRawMaterialWarehouse(Request $request, $id)
    {
        $start_date = $request->start_date ?: now()->format('Y-m-d');
        $end_date   = $request->end_date ?: now()->format('Y-m-d');

        $warehouse = Warehouse::findOrFail($id);

        $lots = RawMaterialLot::with([
            'warehouse',
            'material',
        ])
        ->where('warehouse_id', $id)
        ->where('available_quantity', '>', 0)
        ->get();

        $summary = (object) [
            'lots_count' => $lots->count(),

            'materials_count' => $lots
                ->pluck('raw_material_id')
                ->unique()
                ->count(),

            'quantity' => $lots->sum(function ($lot) {
                return (float) $lot->available_quantity;
            }),

            'total' => $lots->sum(function ($lot) {
                return
                    (float) $lot->available_quantity *
                    (float) $lot->cost;
            }),
        ];

        $pdf = PDF::loadView('admin.pdf.raw-material-warehouse', [
            'warehouse' => $warehouse,
            'lots'      => $lots,
            'summary'   => $summary,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "almacen-materias-primas-{$warehouse->id}.pdf"
        );
    }


    /**
     * ============================================================
     * PDF - TODOS LOS ALMACENES DE PRODUCTO TERMINADO
     * ============================================================
     */
    public function printProductWarehouses(Request $request)
    {
        $start_date = $request->start_date ?: now()->format('Y-m-d');
        $end_date   = $request->end_date ?: now()->format('Y-m-d');

        $warehouses = $this->productWarehouseResume();

        $pdf = PDF::loadView('admin.pdf.product-warehouses', [
            'warehouses' => $warehouses,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "almacenes-productos-{$start_date}-{$end_date}.pdf"
        );
    }


    /**
     * ============================================================
     * PDF - ALMACÉN INDIVIDUAL DE PRODUCTO TERMINADO
     * ============================================================
     */
    public function printProductWarehouse(Request $request, $id)
    {
        $start_date = $request->start_date ?: now()->format('Y-m-d');
        $end_date   = $request->end_date ?: now()->format('Y-m-d');

        $warehouse = Warehouse::findOrFail($id);

        $lots = ProductLot::with([
            'warehouse',
            'product.manufactured',
        ])
        ->where('warehouse_id', $id)
        ->where('available_quantity', '>', 0)
        ->get();

        $summary = (object) [
            'lots_count' => $lots->count(),

            'products_count' => $lots
                ->pluck('product_id')
                ->unique()
                ->count(),

            'quantity' => $lots->sum(function ($lot) {
                return (float) $lot->available_quantity;
            }),

            'total' => $lots->sum(function ($lot) {
                return (float) $lot->total_cost;
            }),
        ];

        $pdf = PDF::loadView('admin.pdf.product-warehouse', [
            'warehouse' => $warehouse,
            'lots'      => $lots,
            'summary'   => $summary,
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);

        return $pdf->stream(
            "almacen-productos-{$warehouse->id}.pdf"
        );
    }

}