<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TypeRequest;
use App\Models\Type;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TypeController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.categories') || Gate::allows('create.categories'), 403);
        $types = Type::all();
        return view('admin.categorias.index', compact('types'));   
    }

    public function save(TypeRequest $request)
    {
        abort_unless(Gate::allows('view.categories') || Gate::allows('edit.categories'), 403);
        
        $type = new Type;
        $type->name = $request->name;
        $type->key_name = Str::slug($request->name);
        $type->save();

        alert('Se ha agregado una categoría.');

        return response('', 204, [
            'Redirect-To' => url('admin/categorias/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.categories') || Gate::allows('edit.categories'), 403);
        $type = Type::find($id);

        return view('admin.categorias.editar', compact('type'));
    }


    public function update(TypeRequest $request, $id)
    {
        abort_unless(Gate::allows('view.categories') || Gate::allows('edit.categories'), 403);

        $types = Type::find($id);
        $types->name = $request->name;
        $types->key_name = Str::slug($request->name);
        $types->save();

        alert('Se ha actualizado una categoría.');

        return response('', 204, [
            'Redirect-To' => url('admin/categorias/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.categories') || Gate::allows('create.categories'), 403);

        $type = Type::find($id);
        $type->delete();
        return response('', 204);

    }
}
