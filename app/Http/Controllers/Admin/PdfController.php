<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Auth;
use App\Models\Sale;

use Luecano\NumeroALetras\NumeroALetras;

class PdfController extends Controller
{
    
    /* public function pdf($id)
    {
        $dateNow    = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = \Request('start_date') != null ? \Request('start_date') : $dateFormat;
        $end_date   = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;
        
        if (!Auth::user()->isSuperAdmin()) {
            $start_date = \Request('start_date') != null ? \Request('start_date') : $dateFormat;
            $end_date   = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;

            $id = Auth::user()->branch_id;
        }
        $services = Service::whereBetween('date', [$start_date, $end_date])
        ->orderBy('date')
        ->where('branch_id', $id)
        ->get();

        $expenses = Expense::whereBetween('date', [$start_date, $end_date])
        ->orderBy('date')
        ->where('branch_id', $id)
        ->with('type_expense')
        ->get();
        
        $servicesPerPayments = [];
            foreach ($services as $service) {
                foreach ($service->payments as $payment) {
                    $paymentMethod = $payment->name;

                    if (!isset($servicesPerPayments[$paymentMethod])) {
                        $servicesPerPayments[$paymentMethod] = 0;
                    }
    
                    $servicesPerPayments[$paymentMethod] += $payment->pivot->cost;
                }
            }

            $servicesPerPayments = collect($servicesPerPayments);
            $serviceswithinCash  =  collect($servicesPerPayments);
            foreach ($serviceswithinCash as $key => $value) {
                if ($key == 'Efectivo') {
                    $serviceswithinCash->forget($key);
                }
            }

        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y');
        $end_date   = Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y');

        
        
        $pdf = PDF::loadView('admin.pdf.index', compact('services', 'expenses', 'start_date', 'end_date', 'servicesPerPayments', 'serviceswithinCash'));
        $pdf->setPaper('letter', 'portrait'); 
        return $pdf->stream('reporte-ingresos-sucursal.pdf',['Attachment' => false]);
        //return $pdf->download('reporte-ingresos-sucursal.pdf');   
    }


    public function pdfEgreso($id)
    {
        $dateNow    = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = \Request('start_date') != null ? \Request('start_date') : $dateNow->subDays(5)->format('Y-m-d');
        $end_date   = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;
        
        if (!Auth::user()->isSuperAdmin()) {
            $start_date = \Request('start_date') != null ? \Request('start_date') : $dateFormat;
            $end_date   = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;

            $id = Auth::user()->branch_id;
        }
        
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])
        ->orderBy('date')
        ->where('branch_id', $id)
        ->with('type_expense')
        ->get();
        
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y');
        $end_date   = Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y');

        
        
        $pdf = PDF::loadView('admin.pdf.indexExpenses', compact('expenses', 'start_date', 'end_date'));
        $pdf->setPaper('letter', 'portrait'); 
        return $pdf->stream('reporte-gastos-sucursal.pdf',['Attachment' => false]);
        //return $pdf->download('reporte-gasto-sucursal.pdf');   
    }

    public function pdfGasto($id)
    {
        $dateNow    = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');

        $start_date = \Request('start_date') != null ? \Request('start_date') : $dateNow->subDays(5)->format('Y-m-d');
        $end_date   = \Request('end_date') != null ? \Request('end_date') : $dateFormat ;
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])
        ->orderBy('date')
        ->where('type_expense_id', $id)
        ->with('type_expense', 'branch')
        ->get();

        // Agrupar por la relación 'branch'
        $groupedExpenses = $expenses->groupBy('branch.name');
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->format('d/m/Y');
        $end_date   = Carbon::createFromFormat('Y-m-d', $end_date)->format('d/m/Y');

        
        
        $pdf = PDF::loadView('admin.pdf.indexGastos', compact('expenses', 'start_date', 'end_date','groupedExpenses'));
        $pdf->setPaper('letter', 'portrait'); 
        return $pdf->stream('repote-gastos-recurrentes', ['Attachment' => false]); 
    } */

    /* public function pdfSale($id)
    {
        $dateNow    = Carbon::now();
        $dateFormat = $dateNow->format('Y-m-d');
        $service    = Product::find($id);
        
        $formatter = new NumeroALetras();
        $formatter->conector = 'Y';
        $service->letter = $formatter->toMoney($service->cost, 2, 'pesos', 'centavos');
        
        $service->day = Carbon::createFromFormat('Y-m-d', $service->date)->format('d');
        $pdf = PDF::loadView('admin.pdf.notesale', compact('service'));
        $pdf->setPaper('letter', 'portrait'); 
        return $pdf->stream();
        //return $pdf->download('report.pdf');   
    } */

    public function pdfSale($id)
    {
        
        $sale = Sale::with('templates', 'user.customer')->find($id);
        $pdf = PDF::loadView('admin.pdf.notesale', compact('sale'));
        $pdf->setPaper('letter', 'portrait'); 
        return $pdf->stream();
        //return $pdf->download('report.pdf');   
    }
}
