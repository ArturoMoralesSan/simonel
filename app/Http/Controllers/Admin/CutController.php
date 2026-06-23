<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CutRequest;
use Illuminate\Http\Request;
use App\Models\Cut;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CutController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.measures'), 403);
        $cuts = Cut::all();
        return view('admin.acabados.index', compact('cuts'));   
    }

    public function save(CutRequest $request)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('edit.measures'), 403);
        
        $cut = new Cut;
        $cut->name = $request->name;
        $cut->key_name = Str::slug($request->name);
        $cut->measure = $request->measure;
        $cut->cost = $request->cost;
        $cut->save();

        alert('Se ha agregado una presentación.');

        return response('', 204, [
            'Redirect-To' => url('admin/acabados/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('edit.measures'), 403);
        $cut = Cut::find($id);

        return view('admin.acabados.editar', compact('cut'));
    }


    public function update(CutRequest $request, $id)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('edit.measures'), 403);

        $cut = Cut::find($id);
        $cut->name = $request->name;
        $cut->key_name = Str::slug($request->name);
        $cut->measure = $request->measure;
        $cut->cost = $request->cost;
        $cut->save();

        alert('Se ha actualizado una presentación.');

        return response('', 204, [
            'Redirect-To' => url('admin/acabados/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.measures'), 403);

        $cut = Cut::find($id);
        $cut->delete();
        
        return response('', 204);

    }
}
