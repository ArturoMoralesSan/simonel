<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index()
    {
        abort_unless(
            Gate::allows('view.suppliers') || Gate::allows('create.suppliers'), 403);

        $search = request('search');

        $suppliers = Supplier::query()
            ->when($search, function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                ->orWhere('trade_name', 'like', "%{$search}%")
                ->orWhere('contact_name', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%");
            })
            ->orderBy('business_name')
            ->paginate(20);
   
        $supplierItems = Collect($suppliers->items());
        $links = $suppliers->appends(request()->query())->links('layout.pagination');

        return view(
            'admin.proveedores.index',
            compact('supplierItems', 'links')
        );
    }


    public function save(SupplierRequest $request)
    {
        abort_unless(Gate::allows('create.suppliers'), 403);

        Supplier::create($request->validated());

        alert('Se ha creado un proveedor.');

        return response('', 204, [
            'Redirect-To' => url('admin/proveedores')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('create.suppliers'), 403);

        $supplier = Supplier::findOrFail($id);

        return view('admin.proveedores.editar', compact('supplier'));
    }

    public function update(SupplierRequest $request, $id) 
    {
        abort_unless(Gate::allows('create.suppliers'),403);

        $supplier = Supplier::findOrFail($id);

        $supplier->update($request->validated());

        alert('Se ha actualizado el proveedor.');

        return response('', 204, [
            'Redirect-To' => url('admin/proveedores')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.suppliers'), 403);

        $supplier = Supplier::findOrFail($id);

        $supplier->delete();

        return response('', 204);
    }
}
