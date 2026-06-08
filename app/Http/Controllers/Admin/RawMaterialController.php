<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use Illuminate\Http\Request;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;


class RawMaterialController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);
        $material = RawMaterial::all();
        return view('admin.materia-prima.index', compact('material'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);

        $material_types = Collect([
            'Carne' => 'Carne',
            'Ingrediente'  => 'Ingrediente',
            'Condimento'  => 'Condimento',
            'Envase'  => 'Envase',
            'Suministros'  => 'Suministros',
        ]);

        $active = Collect([
            '1' => 'Sí',
            '1'  => 'No',
        ]);
        return view('admin.materia-prima.crear', compact('material_types', 'active'));
    }

    public function save(MaterialRequest $request)
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);
        
        $material = new RawMaterial;
        $material->name = $request->name;
        $material->sku = $request->sku;
        $material->raw_material_type = $request->material_types;
        $material->minimum_stock = $request->stock;
        $material->unit = $request->unit;
        $material->cost = $request->cost;
        $material->expiration_days = $request->expiration_days;
        $material->active = 1;
        $material->save();

        alert('Se ha agregado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/materia-prima/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);
        $material = RawMaterial::find($id);
        $material_types = Collect([
            'Carne' => 'Carne',
            'Ingrediente'  => 'Ingrediente',
            'Condimento'  => 'Condimento',
            'Envase'  => 'Envase',
            'Suministros'  => 'Suministros',
        ]);

        $active = Collect([
            '1' => 'Sí',
            '1'  => 'No',
        ]);

        return view('admin.materia-prima.editar', compact('material', 'material_types', 'active'));
    }


    public function update(MaterialRequest $request, $id)
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);

        $material = RawMaterial::find($id);
        $material->name = $request->name;
        $material->sku = $request->sku;
        $material->raw_material_type = $request->material_types;
        $material->minimum_stock = $request->stock;
        $material->unit = $request->unit;
        $material->cost = $request->cost;
        $material->expiration_days = $request->expiration_days;
        $material->active = 1;
        $material->save();

        alert('Se ha actualizado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/materia-prima/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.rawmaterials') || Gate::allows('create.rawmaterials'), 403);

        $cut = RawMaterial::find($id);
        $cut->delete();
        
        return response('', 204);

    }
}
