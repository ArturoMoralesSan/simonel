<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DB;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\SaleProduct;
use App\Models\ProductLot;
use App\Models\Inventory;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isSuperAdmin()) {

            $dateNow     = Carbon::now();
            $dateFormat  = $dateNow->format('Y-m-d');
            $date        = $dateNow->locale('es');
            $string_date = $date->day . ' ' . $date->monthName;

            $start_date  = request('start_date') ?? $dateFormat;
            $end_date    = request('end_date') ?? $dateFormat;

            /*
            |--------------------------------------------------------------------------
            | VENTAS
            |--------------------------------------------------------------------------
            */

            $sale = Sale::where('is_paid', true);

            $sales = (clone $sale)
                ->whereBetween('finish_date', [$start_date, $end_date]);

            $salesNow = (clone $sale)
                ->where('finish_date', $dateFormat);

            $salesCount     = $sales->count();
            $salesNowCount  = $salesNow->count();

            $salesAmount    = $sales->sum('total_with_iva');
            $salesNowAmount = $salesNow->sum('total_with_iva');

            $ingresoNow = number_format($salesNowAmount, 2, '.', ',');
            $ingreso    = number_format($salesAmount, 2, '.', ',');

            /*
            |--------------------------------------------------------------------------
            | VENTAS POR DÍA
            |--------------------------------------------------------------------------
            */

            $days = $sales
                ->orderBy('finish_date')
                ->get()
                ->groupBy(function ($val) {
                    $dateParse = Carbon::parse($val->finish_date)->locale('es');
                    return $dateParse->day . ' ' . $dateParse->monthName;
                })
                ->map(function ($sale) {
                    return $sale->sum('total_with_iva');
                });

            /*
            |--------------------------------------------------------------------------
            | PRODUCTOS MÁS VENDIDOS (CANTIDAD)
            |--------------------------------------------------------------------------
            */

            $productsCount = Product::select(
                    'products.id',
                    'manufactured_products.name'
                )
                ->leftJoin('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
                ->leftJoin('sale_products', 'sale_products.product_id', '=', 'products.id')
                ->leftJoin('sales', 'sales.id', '=', 'sale_products.sale_id')
                ->where(function ($q) use ($start_date, $end_date) {
                    $q->where('sales.is_paid', true)
                    ->whereBetween('sales.finish_date', [$start_date, $end_date]);
                })
                ->selectRaw('COALESCE(SUM(sale_products.quantity),0) as sales_count')
                ->groupBy('products.id', 'manufactured_products.name')
                ->orderByDesc('sales_count')
                ->take(10)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | UTILIDAD ESTIMADA
            |--------------------------------------------------------------------------
            */

            $profit = SaleProduct::selectRaw("
                    SUM(
                        (sale_products.base_price - COALESCE(products.costo_total,0))
                        * sale_products.quantity
                    ) as total_profit
                ")
                ->join('products', 'products.id', '=', 'sale_products.product_id')
                ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                ->where('sales.is_paid', true)
                ->whereBetween('sales.finish_date', [$start_date, $end_date])
                ->value('total_profit') ?? 0;

            /*
            |--------------------------------------------------------------------------
            | TOP PRODUCTOS (KG)
            |--------------------------------------------------------------------------
            */

            $topProducts = SaleProduct::selectRaw("
                    products.id,
                    manufactured_products.name,
                    SUM(sale_products.quantity) as total_kg
                ")
                ->join('products', 'products.id', '=', 'sale_products.product_id')
                ->join('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
                ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                ->where('sales.is_paid', true)
                ->whereBetween('sales.finish_date', [$start_date, $end_date])
                ->groupBy('products.id', 'manufactured_products.name')
                ->orderByDesc('total_kg')
                ->take(10)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | INVENTARIO POR PRODUCTO
            |--------------------------------------------------------------------------
            */

            $inventoryProducts = ProductLot::selectRaw("
                    products.id,
                    manufactured_products.name,
                    SUM(product_lots.available_quantity) as stock
                ")
                ->join('products', 'products.id', '=', 'product_lots.product_id')
                ->join('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
                ->groupBy('products.id', 'manufactured_products.name')
                ->orderByDesc('stock')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | VALOR TOTAL INVENTARIO
            |--------------------------------------------------------------------------
            */

            $inventoryValue = ProductLot::where('available_quantity', '>', 0)->sum('total_cost');

            /*
            |--------------------------------------------------------------------------
            | LOTES BAJOS
            |--------------------------------------------------------------------------
            */

            $lowLots = ProductLot::with('product.manufactured')
                ->where('available_quantity', '<=', 10)
                ->where('available_quantity', '>', 0)
                ->orderBy('available_quantity')
                ->take(20)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | PENDIENTES DE SURTIR
            |--------------------------------------------------------------------------
            */

            $pendingAssortment = Sale::with([
                    'user',
                    'products.product.manufactured'
                ])
                ->where('status', 'paid')
                ->orderBy('created_at')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | CLIENTES CON MÁS VENTAS
            |--------------------------------------------------------------------------
            */

            $customerCount = User::whereHas('sales', function ($q) use ($start_date, $end_date) {
                    $q->where('is_paid', true)
                    ->whereBetween('finish_date', [$start_date, $end_date]);
                })
                ->withCount([
                    'sales as sales_count' => function ($query) use ($start_date, $end_date) {
                        $query->where('is_paid', true)
                            ->whereBetween('finish_date', [$start_date, $end_date]);
                    }
                ])
                ->withSum([
                    'sales as total_sale_price' => function ($query) use ($start_date, $end_date) {
                        $query->where('is_paid', true)
                            ->whereBetween('finish_date', [$start_date, $end_date]);
                    }
                ], 'total_with_iva')
                ->get()
                ->map(function ($customer) {

                    $customer->average_ticket =
                        $customer->sales_count > 0
                            ? ($customer->total_sale_price / $customer->sales_count)
                            : 0;

                    return $customer;
                })
                ->sortByDesc('total_sale_price');

            /*
            |--------------------------------------------------------------------------
            | INVENTARIO CLIENTES
            |--------------------------------------------------------------------------
            */

            $customerInventory = Inventory::selectRaw("
                    users.id,
                    CONCAT(users.name,' ',users.last_name) as customer,
                    SUM(inventories.quantity) as total_quantity
                ")
                ->join('users', 'users.id', '=', 'inventories.user_id')
                ->groupBy('users.id', 'users.name', 'users.last_name')
                ->orderByDesc('total_quantity')
                ->get();

            return view('admin.dashboard-admin', compact(
                'string_date',
                'ingreso',
                'ingresoNow',
                'salesCount',
                'salesNowCount',
                'days',
                'profit',
                'topProducts',
                'productsCount',
                'inventoryProducts',
                'inventoryValue',
                'lowLots',
                'pendingAssortment',
                'customerCount',
                'customerInventory'
            ));
        }

        return view('admin.dashboard');
    }
}
