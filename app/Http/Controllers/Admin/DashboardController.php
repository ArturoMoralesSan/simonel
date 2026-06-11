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
        if (Auth::user()->isSuperAdmin())
        {
            $dateNow     = Carbon::now();
            $dateFormat  = $dateNow->format('Y-m-d');
            $date        = $dateNow->locale('es');
            $string_date = $date->day . ' ' . $date->monthName;

            $start_date  = request('start_date') ?? $dateFormat;
            $end_date    = request('end_date') ?? $dateFormat;

            /*
            |--------------------------------------------------------------------------
            | Ventas
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
            | Ventas por día
            |--------------------------------------------------------------------------
            */

            $days = $sales
                ->orderBy('finish_date')
                ->get()
                ->groupBy(function ($val) {

                    $dateParse = Carbon::parse($val->finish_date);
                    $date      = $dateParse->locale('es');

                    return $date->day . ' ' . $date->monthName;
                })
                ->map(function ($sale) {

                    return $sale->sum('total_with_iva');

                });


            $productsCount = Product::select(
                'products.id',
                'products.name'
            )
            ->leftJoin('sale_products', 'sale_products.product_id', '=', 'products.id')
            ->leftJoin('sales', 'sales.id', '=', 'sale_products.sale_id')
            ->where(function ($q) use ($start_date, $end_date) {
                $q->where('sales.is_paid', true)
                ->whereBetween('sales.finish_date', [$start_date, $end_date]);
            })
            ->selectRaw('COALESCE(SUM(sale_products.quantity),0) as sales_count')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();

            /*
            |--------------------------------------------------------------------------
            | Utilidad estimada
            |--------------------------------------------------------------------------
            */

            $profit = SaleProduct::selectRaw("
                    SUM(
                        (
                            sale_products.base_price -
                            COALESCE(products.costo_total,0)
                        ) * sale_products.quantity
                    ) as total_profit
                ")
                ->join('products', 'products.id', '=', 'sale_products.product_id')
                ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                ->where('sales.is_paid', true)
                ->whereBetween('sales.finish_date', [$start_date, $end_date])
                ->value('total_profit') ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Productos más vendidos (kg)
            |--------------------------------------------------------------------------
            */

            $topProducts = SaleProduct::selectRaw("
                    products.id,
                    products.name,
                    SUM(sale_products.quantity) as total_kg
                ")
                ->join('products', 'products.id', '=', 'sale_products.product_id')
                ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
                ->where('sales.is_paid', true)
                ->whereBetween('sales.finish_date', [$start_date, $end_date])
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_kg')
                ->take(10)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Inventario por producto
            |--------------------------------------------------------------------------
            */

            $inventoryProducts = ProductLot::selectRaw("
                    products.id,
                    products.name,
                    SUM(product_lots.available_quantity) as stock
                ")
                ->join('products', 'products.id', '=', 'product_lots.product_id')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('stock')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Valor total inventario
            |--------------------------------------------------------------------------
            */

            $inventoryValue = ProductLot::where('available_quantity', '>', 0)->sum('total_cost');

            /*
            |--------------------------------------------------------------------------
            | Lotes bajos
            |--------------------------------------------------------------------------
            */

            $lowLots = ProductLot::with('product')
                ->where('available_quantity', '<=', 10)
                ->where('available_quantity', '>', 0)
                ->orderBy('available_quantity')
                ->take(20)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Pendientes de surtir
            |--------------------------------------------------------------------------
            */

            $pendingAssortment = Sale::with([
                    'user',
                    'products.product'
                ])
                ->where('status', 'paid')
                ->orderBy('created_at')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Clientes con más ventas
            |--------------------------------------------------------------------------
            */

            $customerCount = User::whereHas('sales', function ($q) use ($start_date, $end_date) {

                    $q->where('is_paid', true);
                    $q->whereBetween('finish_date', [$start_date, $end_date]);

                })
                ->withCount([
                    'sales',
                    'sales as sales_count' => function ($query) use ($start_date, $end_date) {

                        $query->where('is_paid', true);
                        $query->whereBetween('finish_date', [$start_date, $end_date]);

                    }
                ])
                ->withSum([
                    'sales as total_sale_price' => function ($query) use ($start_date, $end_date) {

                        $query->where('is_paid', true);
                        $query->whereBetween('finish_date', [$start_date, $end_date]);

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
            | Inventario clientes
            |--------------------------------------------------------------------------
            */

            $customerInventory = Inventory::selectRaw("
                    users.id,
                    CONCAT(users.name,' ',users.last_name) as customer,
                    SUM(inventories.quantity) as total_quantity
                ")
                ->join('users', 'users.id', '=', 'inventories.user_id')
                ->groupBy(
                    'users.id',
                    'users.name',
                    'users.last_name'
                )
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

                'inventoryProducts',
                'inventoryValue',
                'lowLots',

                'pendingAssortment',

                'customerCount',
                'customerInventory',
                'productsCount'
            ));
        }

        return view('admin.dashboard');
    }
}
