<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Support\Facades\Gate;

class AssignmentController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.assignment') || Gate::allows('create.assignment'), 403);
        $assignments = User::with('equipments')->get();
        return view('admin.asignaciones.index', compact('assignments'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.assignment') || Gate::allows('create.assignment'), 403);
        $users = User::pluck('name','id');
        $equipments = Equipment::pluck('product','id');
        return view('admin.asignaciones.crear', compact('users', 'equipments'));   
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.assignment') || Gate::allows('create.assignment'), 403);
        $users = User::pluck('name','id');
        $equipments = Equipment::pluck('product','id');
        $user = User::find($id);
        $assigned_products = $user->equipments()->get();

        return view('admin.asignaciones.editar', compact('assigned_products', 'users', 'equipments','user'));   
    }

    public function save(Request $request)
    {
        abort_unless(Gate::allows('view.assignment') || Gate::allows('create.assignment'), 403);
        $user = User::find($request->user);
        $user->equipments()->detach();
        for($i = 1; $i <= $request->product_count; $i++){
            $user->equipments()->attach($request['product'.$i.'_producto'], ['quantity'=> $request['product'.$i.'_quantity']]);
        }
        

        alert('Se ha asignado equipo.');

        return response('', 204, [
            'Redirect-To' => url('admin/lista-equipo-asignado/')
        ]);  
    }
}