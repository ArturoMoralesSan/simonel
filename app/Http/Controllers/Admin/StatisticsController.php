<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Expense;
use App\Models\TypeExpense;
use Auth;
use DB; 

class StatisticsController extends Controller
{
    public function index()
    {
        $years = collect([
            '2023' => '2023',
            '2024' => '2024',
            '2025' => '2025',
            '2026' => '2026',
            '2027' => '2027',
            '2028' => '2028',
            '2029' => '2029',
            '2030' => '2030',
            '2031' => '2031',
            '2032' => '2032',
            '2033' => '2033',
            '2034' => '2034'
        ]);
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

        $date     = \Request('date');
        $month    = \Request('month');
        $year     = \Request('year');
        $branch   = \Request('branch');
        $payment  = \Request('payment');

        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }        
        $payments = Payment::pluck('name','id');

        $costbranches = Branch::with('services.payments');
        $costService = Service::with(['payments'])
        ->withCount(['payments as cost' => function ($query) {
            $query->select(DB::raw('SUM(cost)'));
        }]);
        $paymentspayment = Payment::with('services');

        if ($date) {
            $costbranches = $costbranches->whereHas('services', function ($query) use ($date){
                $query->where('date', $date);
            });
            $costService->where('date', $date);
            $paymentspayment = $paymentspayment->whereHas('services', function ($query) use ($date){
                $query->where('date', $date);
            });
        }
        if ($month) {
            $costbranches = $costbranches->whereHas('services', function ($query) use ($month){
                $query->where('month', $month);
            });
            $costService->where('month', $month);
            $paymentspayment = $paymentspayment->whereHas('services', function ($query) use ($month){
                $query->where('month', $month);
            });
        }
        if ($year) {
            $costbranches = $costbranches->whereHas('services', function ($query) use ($year){
                $query->where('year', $year);
            });
            $costService->where('year', $year);
            $paymentspayment = $paymentspayment->whereHas('services', function ($query) use ($year){
                $query->where('year', $year);
            });
        }
        if ($branch) {
            $costbranches = $costbranches->where('id', $branch);
            $costService->where('branch_id', $branch);
            $paymentspayment = $paymentspayment->whereHas('services', function ($query) use ($branch){
                $query->where('branch_id', $branch);
            });
        }
        if ($payment) {
            $costbranches = $costbranches->whereHas('services', function ($query) use ($payment){
                $query->where('payment_id', $payment);
            });
            $costService->where('payment_id', $payment);
            $paymentspayment = $paymentspayment->where('id', $payment);
        }

        $services_branch_by_amount = [];
        $services_payment_by_amount = [];
        $services_by_amount = [];

        foreach ($costService->get() as $service) {
            $services_by_amount[] = $service->cost;
        }
        
        foreach ($costbranches->get() as $branch) {
            $totalCost = 0;
            foreach ($branch->services as $service) {
                $totalCost += $service->payments()->sum('cost');
            }
            $services_branch_by_amount[$branch->name] = $totalCost;
        }

        foreach ($paymentspayment->get() as $payment) {
            $totalCost = 0;
            foreach ($payment->services as $service) {
                $totalCost += $service->payments()
                    ->wherePivot('payment_id', $payment->id)
                    ->sum('cost');
            }

            $services_payment_by_amount[$payment->name] = $totalCost;
        }

        return view('admin.estadisticas.index', compact('branches','payments','years','months'))
        ->with('services_by_amount',json_encode($services_by_amount,JSON_NUMERIC_CHECK))
        ->with('services_branch_by_amount',json_encode($services_branch_by_amount,JSON_NUMERIC_CHECK))
        ->with('services_payment_by_amount',json_encode($services_payment_by_amount,JSON_NUMERIC_CHECK));


    }

    public function gastos()
    {
        $years = collect([
            '2023' => '2023',
            '2024' => '2024',
            '2025' => '2025',
            '2026' => '2026',
            '2027' => '2027',
            '2028' => '2028',
            '2029' => '2029',
            '2030' => '2030',
            '2031' => '2031',
            '2032' => '2032',
            '2033' => '2033',
            '2034' => '2034'
        ]);
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

        $date     = \Request('date');
        $month    = \Request('month');
        $year     = \Request('year');
        $branch   = \Request('branch');

        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }

        $costbranches    = Branch::Select('name');
        $costExpense     = Expense::groupBy('amount');
        $costTypeExpense = TypeExpense::Select('name');


        if ($date) {
            $costbranches = $costbranches->whereHas('expenses', function ($query) use ($date){
                $query->where('date', $date);
            });
            $costExpense->where('date', $date);
            $costTypeExpense = $costTypeExpense->whereHas('expenses', function ($query) use ($date){
                $query->where('date', $date);
            });
        }
        if ($month) {
            $costbranches = $costbranches->whereHas('expenses', function ($query) use ($month){
                $query->where('month', $month);
            });
            $costExpense->where('month', $month);
            $costTypeExpense = $costTypeExpense->whereHas('expenses', function ($query) use ($month){
                $query->where('month', $month);
            });
        }
        if ($year) {
            $costbranches = $costbranches->whereHas('expenses', function ($query) use ($year){
                $query->where('year', $year);
            });
            $costExpense->where('year', $year);
            $costTypeExpense = $costTypeExpense->whereHas('expenses', function ($query) use ($year){
                $query->where('year', $year);
            });
        }
        if ($branch) {
            $costbranches = $costbranches->where('id', $branch);
            $costExpense->where('branch_id', $branch);
            $costTypeExpense = $costTypeExpense->whereHas('expenses', function ($query) use ($branch){
                $query->where('branch_id', $branch);
            });
        }

        $expenses_branch_by_amount = [];
        $expenses_type_by_amount   = [];
        $branches_by_amount        = $costbranches->withSum('expenses', 'amount')->get();
        $expenses_types_by_amount  = $costTypeExpense->withSum('expenses', 'amount')->get();
        $expenses_by_amount[]      = $costExpense->sum('amount');

        foreach ($branches_by_amount as $branch) {
            $expenses_branch_by_amount[$branch->name] = $branch->expenses_sum_amount;
        }
        foreach ($expenses_types_by_amount as $type) {
            $expenses_type_by_amount[$type->name] = $type->expenses_sum_amount;
        }
        

        return view('admin.estadisticas.gastos', compact('branches','years','months'))
        ->with('expenses_by_amount',json_encode($expenses_by_amount,JSON_NUMERIC_CHECK))
        ->with('expenses_branch_by_amount',json_encode($expenses_branch_by_amount,JSON_NUMERIC_CHECK))
        ->with('expenses_type_by_amount',json_encode($expenses_type_by_amount,JSON_NUMERIC_CHECK));

    }
}
