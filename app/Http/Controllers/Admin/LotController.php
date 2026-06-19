<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LotRequest;
use Illuminate\Http\Request;
use App\Models\RawMaterialLot;
use App\Models\RawMaterial;
use App\Models\Warehouse;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LotController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('create.rawmateriallot'), 403);
        $lots = RawMaterialLot::with(['supplier', 'material', 'warehouse'])->get();
        return view('admin.lotes.index', compact('lots'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('edit.rawmateriallot'), 403);

        $status = Collect([
            'Disponible' => 'Disponible',
            'Reservado' => 'Reservado',
            'Consumido' => 'Consumido',
            'Expirado' => 'Expirado'
        ]);

        $raw_materials = RawMaterial::pluck('name','id');
        $suppliers = Supplier::pluck('business_name','id');
        $warehouses = Warehouse::pluck('name','id');

        
        return view('admin.lotes.crear', compact('suppliers', 'status', 'raw_materials', 'warehouses'));
    }

    public function save(LotRequest $request)
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('edit.rawmateriallot'), 403);

        $rawMaterial = RawMaterial::findOrFail($request->raw_material_id);

        $lot = new RawMaterialLot;
        $lot->raw_material_id = $request->raw_material_id;
        $lot->warehouse_id = $request->warehouse_id;
        $lot->lot_number = 'LOT-' . date('Ymd') . '-' . str_pad($lastLot, 5, '0', STR_PAD_LEFT);
        $lot->supplier_id = $request->supplier_id;
        $lot->entry_date = $request->entry_date;
        $lot->expiration_date = $rawMaterial->expiration_days
            ? Carbon::parse($request->entry_date)
                ->addDays($rawMaterial->expiration_days)
                ->format('Y-m-d')
            : null;

        $lot->initial_quantity = $request->initial_quantity;
        $lot->available_quantity = $request->available_quantity;
        $lot->cost = $request->cost;
        $lot->status = 'Disponible';
        $lot->save();

        alert('Se ha agregado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/lotes/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('edit.rawmateriallot'), 403);
        $lot = RawMaterialLot::find($id);
        $status = Collect([
            'Disponible' => 'Disponible',
            'Reservado' => 'Reservado',
            'Consumido' => 'Consumido',
            'Expirado' => 'Expirado'
        ]);
        $suppliers = Supplier::pluck('business_name','id');
        $raw_materials = RawMaterial::pluck('name','id');
        $warehouses = Warehouse::pluck('name','id');

        
        return view('admin.lotes.editar', compact('suppliers', 'lot','status', 'raw_materials', 'warehouses'));
    }


    public function update(LotRequest $request, $id)
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('edit.rawmateriallot'), 403);

        $lot = RawMaterialLot::find($id);

        $rawMaterial = RawMaterial::findOrFail($request->raw_material_id);

        $lot->raw_material_id = $request->raw_material_id;
        $lot->warehouse_id = $request->warehouse_id;
        $lot->supplier_id = $request->supplier_id;
        $lot->entry_date = $request->entry_date;
        $lot->expiration_date = $rawMaterial->expiration_days
            ? Carbon::parse($request->entry_date)
                ->addDays($rawMaterial->expiration_days)
                ->format('Y-m-d')
            : null;

        $lot->initial_quantity = $request->initial_quantity;
        $lot->available_quantity = $request->available_quantity;
        $lot->cost = $request->cost;
        $lot->status = 'Disponible';
        $lot->save();

        alert('Se ha actualizado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/lotes/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.rawmateriallot') || Gate::allows('create.rawmateriallot'), 403);

        $lot = RawMaterialLot::find($id);
        $lot->delete();
        
        return response('', 204);

    }
}
