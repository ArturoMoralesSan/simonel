<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('create.roles'), 403);
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('create.roles'), 403);
        $permissions = Permission::all();
        return view('admin.roles.crear', compact('permissions'));   
    }

    public function save(RoleRequest $request)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.roles'), 403);

        $role = new Role;
        $role->name = $request->name;
        $role->key_name = Str::slug($request->name);
        $role->save();

        $permissions = [];

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'permissions_') && $value === 'true') {
                $permissions[] = str_replace('permissions_', '', $key);
            }
        }

        $role->permissions()->sync($permissions);

        alert('Se ha agregado un rol.');

        return response('', 204, [
            'Redirect-To' => url('admin/roles/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.roles'), 403);
        $permissions = Permission::all();
        $role = Role::find($id);

        return view('admin.roles.editar', compact('role', 'permissions'));
    }


    public function update(RoleRequest $request, $id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('edit.Role'), 403);

            $role = Role::find($id);
            if($role->key_name != 'superadmin')
            {
                $permissions = collect($request->all())
                    ->filter(function ($value, $key) {
                        return str_starts_with($key, 'permissions_') && $value === "true";
                    })
                    ->keys()
                    ->map(function ($key) {
                        return str_replace('permissions_', '', $key);
                    })
                    ->toArray();
            } 
        
            $role->name = $request->name;
            $role->save();
            if($role->key_name != 'superadmin')
            {
                $role->permissions()->sync($permissions);
            }

            alert('Se ha actualizado un permiso.');

            return response('', 204, [
                'Redirect-To' => url('admin/roles/')
            ]);
        }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.roles') || Gate::allows('create.roles'), 403);

        $role = Role::find($id);
        $role->delete();
        
        return response('', 204);

    }
}
