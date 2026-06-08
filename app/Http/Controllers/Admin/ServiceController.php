<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Study;
use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ServiceRequest;
use Carbon\Carbon;
use Auth;

class ServiceController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);
        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $years = collect([]);

        $año_actual = Carbon::now()->year;

        for ($año = 2023; $año <= $actual_year; $año++) {
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

        $search = \Request('search');

        $month = \Request('month') != null ? \Request('month') : $actual_month;
        $year  = \Request('year') != null ? \Request('year') : $actual_year;

        $services = Service::with('branch:id,name','studies')
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->orderBy('date','DESC');
        

        if (!Auth::user()->isSuperAdmin()) {
            $services = $services->where('branch_id', Auth::user()->branch_id);
        }
        
        if($search) {
            $services = $services->where('patient', 'LIKE', '%'.$search.'%');
        }

        $services = $services->paginate(20);

        $services->getCollection()->transform(function ($service) {
            $service->branch->setAppends([]);
            return $service;
        });
        
        $servicesItems = Collect($services->items());

        return view('admin.servicios.index', compact('services', 'servicesItems', 'years', 'months', 'actual_month', 'actual_year')); 
    }

    public function create()
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);
        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }        
        $payments = Payment::pluck('name','id');
        $studies = Study::pluck('name','id');
        return view('admin.servicios.crear', compact('branches', 'studies', 'payments'));   
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);
        if (!Auth::user()->isSuperAdmin()) {
            $branches = Branch::where('id', Auth::user()->branch_id)->pluck('name','id');
        } else {
            $branches = Branch::pluck('name','id');
        }        
        $payments = Payment::pluck('name','id');
        $studies = Study::pluck('name','id');
        $service = Service::find($id);
        
        $assigned_studies = $service->studies()->get();
        $assigned_payments = $service->payments()->get();
        return view('admin.servicios.editar', compact('assigned_payments','assigned_studies', 'branches', 'studies','service', 'payments'));   
    }

    public function save(ServiceRequest $request)
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);
        
        if ($request->service_id == null) {
            $service = New Service;
            $date = Carbon::createFromFormat('Y-m-d', $request->date);
            $service->date = $request->date;

            $last_service = Service::where('branch_id', $request->branch_id)
            ->orderBy('id','DESC')
            ->first();
            if ($last_service) {
                if ($last_service->folio == null) {
                    $number_next = 0;
                    
                } else {
                    $number_next = intval(substr($last_service->folio, 1));
                }
            } else {
                $number_next = 0;
            }
            

            $number_next = $number_next + 1;

            $branch_name = Branch::find($request->branch_id)->name;
            $first_char = mb_substr($branch_name, 0, 1);

            $prev_folio = str_pad($first_char, 6, '0', STR_PAD_RIGHT);
            $folio      = str_pad($prev_folio, 7, $number_next, STR_PAD_RIGHT);
            
        } else {
            $service = Service::find($request->service_id);
            $date    = Carbon::createFromFormat('Y-m-d', $service->date);
            $folio   = $service->folio;
        }

        if($request->print == 'No') {
            $no_rx = 0;
        } else {
            $no_rx = $request->no_rx;
        }
        $service->folio = $folio;
        $service->patient = $request->patient;
        $service->last_rx = $request->rx_prev;
        $service->branch_id = $request->branch_id;
        $service->year = $date->year;
        $service->month = $date->month;
        $service->week = $date->week;
        $service->print = $request->print;
        $service->no_rx = $no_rx;
        $service->save();

        $service->studies()->detach();
        for($i = 1; $i <= $request->studies_count; $i++){
            $service->studies()->attach($request['study'.$i.'_estudio']);
        }

        $service->payments()->detach();
        for($i = 1; $i <= $request->payments_count; $i++){
            $service->payments()->attach($request['payment'.$i.'_estudio'], ['cost'=> $request['payment'.$i.'_cost']]);
        }
        
        if ($request->service_id == null) {
            alert('Se ha agregado un servicio.');
        } else {
            alert('Se ha editado un servicio.');
        }
        

        return response('', 204, [
            'Redirect-To' => url('admin/servicios/')
        ]);  
    }

    public function getrx(Request $request, $id)
    {
        $last_service = Service::where('branch_id',$id)
        ->orderBy('id', 'DESC')
        ->first();
        if ($last_service) {
            $last_rx = $last_service->last_rx - $last_service->no_rx;
            if ($last_rx < 0) {
                $last_rx = 0;
            }
            return response()->json($last_rx);
        } else {
            return response()->json(0);
        }
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.services') || Gate::allows('create.services'), 403);

        $service = Service::find($id);
        $service->payments()->detach();
        $service->delete();

        return response('', 204);

    }
}
