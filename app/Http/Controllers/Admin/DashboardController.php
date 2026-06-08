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

class DashboardController extends Controller
{
    public function index()
    {
        
        if (Auth::user()->isSuperAdmin()) 
        {
            $dateNow     = Carbon::now();
            $dateFormat  = $dateNow->format('Y-m-d');
            $date        = $dateNow->locale('es');
            $string_date = $date->day.' ' .$date->monthName;
            $start_date  = \Request('start_date') != null ? \Request('start_date') : $dateFormat;
            $end_date    = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;
            
            $sale     = Sale::where('is_paid', true);
            $sales    = (clone $sale)->whereBetween('finish_date', [$start_date, $end_date]);
            $salesNow = (clone $sale)->where('finish_date', $dateFormat);

            if($sales->get()->isEmpty()) {
                $salesCount     = 0;
                $salesNowCount  = 0;
                $salesAmount    = 0;
                $salesNowAmount = 0;
            } else {
                $salesCount    = $sales->count();
                $salesAmount   = $sales->sum('total_with_iva');
                $salesNowCount = $salesNow->count();
                $salesNowAmount= $salesNow->sum('total_with_iva');
   
            } 
            
            $days = $sales->orderBy('finish_date')
            ->get()
            ->groupBy(function ($val) {
                $dateParse   = Carbon::parse($val->finish_date);
                $date        = $dateParse->locale('es');
                return $date->day.' ' .$date->monthName;
            })->map(function ($sale) {
                return $sale->sum('total_with_iva');
            }); 

            $ingresoNow = number_format($salesNowAmount, 2, '.', ',');
            $ingreso = number_format($salesAmount, 2, '.', ',');


            $productsCount = Product::whereHas('templates.sales', function($q) use ($start_date, $end_date) {
                $q->where('is_paid', true);
                $q->whereBetween('finish_date', [$start_date, $end_date]);
            })
            ->withCount([
                'templates as sales_count' => function ($query) use ($start_date, $end_date) {
                    $query->whereHas('sales', function($q) use ($start_date, $end_date) {
                        $q->where('is_paid', true);
                        $q->whereBetween('finish_date', [$start_date, $end_date]);
                    });
                }
            ])
            ->orderBy('sales_count', 'desc')
            ->get();
                
            $customerCount = User::whereHas('sales', function($q) use ($start_date, $end_date) {
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
            ->orderBy('total_sale_price', 'desc')
            ->get();

            return view('admin.dashboard-admin', compact('string_date', 
            'ingreso','ingresoNow', 'salesCount','salesNowCount', 'days', 'productsCount', 'customerCount'));   
            
        } else {

            return view('admin.dashboard');
        }
    }
}
