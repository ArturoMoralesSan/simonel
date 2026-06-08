<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\PermissionRequest;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.permissions') || Gate::allows('create.permissions'), 403);
        $permissions = Permission::all();
        return view('admin.permisos.index', compact('permissions'));   
    }

    public function save(PermissionRequest $request)
    {
        abort_unless(Gate::allows('view.permissions') || Gate::allows('edit.permissions'), 403);
        
        $permission = new Permission;
        $permission->name = $request->name;
        $permission->key_name = Str::slug($request->name);
        $permission->save();

        alert('Se ha agregado un permiso.');

        return response('', 204, [
            'Redirect-To' => url('admin/permisos/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.permissions') || Gate::allows('edit.permissions'), 403);
        $permission = Permission::find($id);

        return view('admin.permisos.editar', compact('permission'));
    }


    public function update(PermissionRequest $request, $id)
    {
        abort_unless(Gate::allows('view.permissions') || Gate::allows('edit.permission'), 403);

        $permission = Permission::find($id);
        $permission->name = $request->name;
        $permission->key_name = Str::slug($request->name);
        $permission->save();

        alert('Se ha actualizado un permiso.');

        return response('', 204, [
            'Redirect-To' => url('admin/permisos/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.permissions') || Gate::allows('create.permissions'), 403);

        $permission = Permission::find($id);
        $permission->delete();

        return response('', 204);

    }
}
