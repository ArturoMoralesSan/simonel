<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturedRequest;
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

        $search = request('search');

        $catalogo = ManufacturedProduct::withCount('recipes')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'ASC')
            ->get();
        return view('admin.catalogo.index', compact('catalogo'));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        return view('admin.catalogo.crear');
    }

    public function save(ManufacturedRequest $request)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $manufactured = ManufacturedProduct::create([
            'name' => $request->name,
            'description' => $request->desc
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

    public function update(ManufacturedRequest $request, $id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        
        $manufactured = ManufacturedProduct::findOrFail($id);
        $manufactured->update([
            'name' => $request->name,
            'description' => $request->desc
        ]);

        alert('Se ha actualizado un elemento.');

        return response('', 204, [
            'Redirect-To' => url('admin/catalogo/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        
        $manufactured = ManufacturedProduct::findOrFail($id);
        $manufactured->delete();

        return response('', 204);
    }
}
