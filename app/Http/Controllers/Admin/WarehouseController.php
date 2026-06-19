<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);
        $warehouses = Warehouse::all();
        return view('admin.almacenes.index', compact('warehouses'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);

         $warehouse_types = Collect([
            'Materia prima' => 'Materia prima',
            'Producción' => 'Producción',
            'Refrigeración' => 'Refrigeración',
            'Congelador' => 'Congelador',
            'Zona de ventas' => 'Zona de ventas',
            'Productos terminados' => 'Productos terminados',
        ]);

        return view('admin.almacenes.crear', compact('warehouse_types'));
    }

    public function save(WarehouseRequest $request)
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);
        
        $warehouse = new Warehouse;
        $warehouse->name = $request->name;
        $warehouse->warehouse_type = $request->warehouse_type;
        $warehouse->active = 1;
        $warehouse->save();

        alert('Se ha agregado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/almacenes/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);
        $warehouse = Warehouse::find($id);
        $warehouse_types = Collect([
            'Materia prima' => 'Materia prima',
            'Producción' => 'Producción',
            'Refrigeración' => 'Refrigeración',
            'Congelador' => 'Congelador',
            'Zona de ventas' => 'Zona de ventas',
            'Productos terminados' => 'Productos terminados',
        ]);


        return view('admin.almacenes.editar', compact('warehouse', 'warehouse_types'));
    }


    public function update(WarehouseRequest $request, $id)
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);

        $warehouse = Warehouse::find($id);
        $warehouse->name = $request->name;
        $warehouse->warehouse_type = $request->warehouse_type;
        $warehouse->save();

        alert('Se ha actualizado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/almacenes/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.warehouses') || Gate::allows('create.warehouses'), 403);

        $cut = Warehouse::find($id);
        $cut->delete();
        
        return response('', 204);

    }
}
