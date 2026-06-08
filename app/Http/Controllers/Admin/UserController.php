<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\UserAdminRequest;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Hash;
use Auth;
use Illuminate\Support\Collection;


class UserController extends Controller
{

    public function index()
    {
       abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        $users = User::with('role')->where('role_id','<>', '2')->get();

        return view('admin.usuarios.index', compact('users'));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        $roles = Role::pluck('name','id');

        return view('admin.usuarios.crear', compact('roles'));
    }


    public function save(UserAdminRequest $request)
    {
        abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        $password        = $request->password;
        $user            = new User;
        $user->name      = $request->name;
        $user->last_name = $request->last_name;
        $user->email     = $request->email;
        $user->username  = $request->username;
        $user->password =  Hash::make($password);
        $user->role_id  = $request->role_id;
        $user->save();

        alert('Se ha agregado un usuario.');

        return response('', 204, [
            'Redirect-To' => url('admin/usuarios/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        $user     = User::find($id);
        $roles    = Role::pluck('name','id');

        return view('admin.usuarios.editar', compact('roles', 'user'));
    }

    public function update(UserAdminRequest $request, $id)
    {
        abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        $user      = User::find($id);

        if ($request->password) {
            $user->password =  Hash::make($request->password);        }

        $user->name      = $request->name;
        $user->last_name = $request->last_name;
        $user->email     = $request->email;
        $user->username  = $request->username;
        $user->role_id   = $request->role_id;
        $user->save();

        alert('Se ha actualizado un usuario.');

        return response('', 204, [
            'Redirect-To' => url('admin/usuarios/')
        ]);
    }

    public function destroy($id)
    {
        abort_unless(Gate::allows('view.users') || Gate::allows('create.users'), 403);

        if (Auth::user()->id !== $id) {
            $user = User::find($id);
            $user->delete();
            alert('Se ha eliminado un usuario.');
        }

        alert('No se ha podido eliminar un usuario.');
        return response('', 204);

    }
}
