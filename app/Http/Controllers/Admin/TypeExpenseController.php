<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeExpense;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\TypeExpenseRequest;
use Illuminate\Support\Str;

class TypeExpenseController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.type_expenses') || Gate::allows('create.type_expenses'), 403);
        $type_expenses = TypeExpense::all();
        return view('admin.tipogastos.index', compact('type_expenses'));   
    }

    public function save(TypeExpenseRequest $request)
    {
        abort_unless(Gate::allows('view.type_expenses') || Gate::allows('edit.type_expenses'), 403);
        
        $type_expense = new TypeExpense;
        $type_expense->name = $request->name;
        $type_expense->key_name = Str::slug($request->name);
        $type_expense->save();

        alert('Se ha agregado un tipo de gasto.');

        return response('', 204, [
            'Redirect-To' => url('admin/tipos-gastos/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.type_expenses') || Gate::allows('edit.type_expenses'), 403);
        $type_expense = TypeExpense::find($id);
        return view('admin.tipogastos.editar', compact('type_expense'));
    }


    public function update(TypeExpenseRequest $request, $id)
    {
        abort_unless(Gate::allows('view.type_expenses') || Gate::allows('edit.type_expenses'), 403);

        $type_expense = TypeExpense::find($id);
        $type_expense->name = $request->name;
        
        $type_expense->key_name = Str::slug($request->name);
        $type_expense->save();

        alert('Se ha actualizado un tipo de gasto.');

        return response('', 204, [
            'Redirect-To' => url('admin/tipos-gastos/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.type_expenses') || Gate::allows('create.type_expenses'), 403);

        $type_expense = TypeExpense::find($id);
        $type_expense->delete();

        return response('', 204);

    }
}
