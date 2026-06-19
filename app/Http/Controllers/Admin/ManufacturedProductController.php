<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ManufacturedProduct;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ManufacturedProductController extends Controller
{
    
    public function index()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $catalogo = ManufacturedProduct::orderBy('id', 'desc')->get();

        return view('admin.catalogo.index', compact('catalogo'));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        return view('admin.catalogo.crear');
    }

    public function save(Request $request)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $manufactured = ManufacturedProduct::create([
            'name' => $request->name,
        ]);

            
        alert('Se ha agregado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/catalogo/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $catalogo = ManufacturedProduct::findOrFail($id);

        return view('admin.catalogo.editar', compact('catalogo'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        
        $manufactured = ManufacturedProduct::findOrFail($id);
        $manufactured->update([
            'name' => $request->name,
        ]);

        alert('Se ha agregado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/catalogo/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        
        $manufactured = ManufacturedProduct::findOrFail($id);
        $manufactured->delete();

        return redirect()->route('manufactured-products.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}
