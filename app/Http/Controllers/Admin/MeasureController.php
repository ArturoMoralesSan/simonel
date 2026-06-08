<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeasureRequest;
use Illuminate\Http\Request;
use App\Models\Measure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class MeasureController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.mesures'), 403);
        $measures = Measure::all();
        return view('admin.medidas.index', compact('measures'));   
    }

    public function save(MeasureRequest $request)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.measures'), 403);
        
        $measure = new Measure;
        $measure->name = $request->name;
        $measure->key_name = Str::slug($request->name);
        $measure->save();

        alert('Se ha agregado una medida.');

        return response('', 204, [
            'Redirect-To' => url('admin/medidas/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.measures'), 403);
        $measure = Measure::find($id);

        return view('admin.medidas.editar', compact('measure'));
    }


    public function update(MeasureRequest $request, $id)
    {
        abort_unless(Gate::allows('views.measures') || Gate::allows('create.measures'), 403);

        $measure = Measure::find($id);
        $measure->name = $request->name;
        $measure->save();

        alert('Se ha actualizado una medida.');

        return response('', 204, [
            'Redirect-To' => url('admin/medidas/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.measures') || Gate::allows('create.mesures'), 403);

        $measure = Measure::find($id);
        $measure->delete();
        
        return response('', 204);

    }
}
