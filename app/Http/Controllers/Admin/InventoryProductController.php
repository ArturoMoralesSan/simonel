<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryProductRequest;
use App\Http\Requests\InventoryRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InventoryProductController extends Controller
{

    public function updateMovement(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'sale_id' => 'nullable|max:20',
            'name' => 'nullable|max:120',
        ]);

        $movement = InventoryMovement::with([
            'inventory',
            'inventory.product',
            'inventory.movements'
        ])->findOrFail($id);

        $inventory = $movement->inventory;

        if (!$inventory) {

            return response()->json([
                'error' => 'Inventario no encontrado'
            ], 404);
        }

        $product = $inventory->product;

        if (!$product) {

            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }

        $movement->date = $validated['date'];
        $movement->quantity = (float) $validated['quantity'];

        if ($movement->type === 'salida') {

            $movement->sale_id =
                $validated['sale_id'] ?? null;

            $movement->name =
                $validated['name'] ?? null;
        }

        $movement->save();

        $entradas = $inventory->movements()
            ->where('type', 'entrada')
            ->sum('quantity');

        $salidas = $inventory->movements()
            ->where('type', 'salida')
            ->sum('quantity');

        $stockReal = $entradas - $salidas;

        if ($stockReal < 0) {

            return response()->json([
                'error' => 'Stock insuficiente'
            ], 422);
        }

        $inventory->quantity = $stockReal;
        $inventory->total_value =
            $stockReal * $product->costo_venta;

        $inventory->save();

        return response()->json([
            'message' => 'Movimiento actualizado correctamente',
            'movement' => $movement->fresh(),
            'inventory' => $inventory->fresh(),
        ]);
    }

    
    public function index()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('create.inventories'), 403);

        $search = request('search');
        $query = Inventory::with('product.cut')->where('inventory_type', "Interno")->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $paginatedInventories = $query->paginate(10)->appends(request()->all());;
        $inventoriesItems = Collect($paginatedInventories->items());
        $links = $paginatedInventories->links('layout.pagination');

        return view('admin.inventarioproductos.index', compact('inventoriesItems', 'links'));
    }

    
    public function details($id)
    {
        $inventory = Inventory::with('product.cut')
            ->where('id', $id)
            ->first();

        $movements = InventoryMovement::with('inventory.product')
            ->where('inventory_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $labels = [
            'entradas' => 'Entradas',
            'salidas'  => 'Salidas',
            'Resumen' => 'Resumen',
        ];

        $movementsEntradas = $movements->where('type', 'entrada')->values();
        $movementsSalidas = $movements->where('type', 'salida')->values();

        return view('admin.inventarioproductos.details', compact('inventory', 'labels', 'movementsEntradas', 'movementsSalidas'));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
            $tags = Collect([
                'Etiqueta' => 'Etiqueta',
                'Sin etiqueta'  => 'Sin etiqueta',
            ]);

            $types = Collect([
                'Interno' => 'Interno',
                'Externo'  => 'Externo',
            ]);

        $products = Product::selectRaw("
            products.id,
            CONCAT(products.name, ' (', cuts.name, cuts.measure, ')') as full_name
        ")
        ->join('cuts', 'cuts.id', '=', 'products.cut_id')
        ->pluck('full_name', 'id');

        return view('admin.inventarioproductos.crear', compact('products', 'tags', 'types'));
    }
    

    public function save(InventoryProductRequest $request)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $product = Product::find($request->product_id);
        $total = $product->costo_venta * $request->quantity;
        $inventory = new Inventory;
        $inventory->product_id  = $request->product_id;
        $inventory->tag   = $request->tag;
        $inventory->inventory_type = $request->inventory_type;
        $inventory->quantity_min = $request->quantity_min;
        $inventory->quantity     = $request->quantity;
        $inventory->total_value  = $total;
        $inventory->save();

        //Inventory::checkStock($inventory);


        alert('Se ha agregado un elemento al inventario.');

        return response('', 204, [
            'Redirect-To' => url('admin/inventario/')
        ]);
    }


    public function update(Request $request, $id)
     {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);

        $inventory = Inventory::with('product')
            ->where('id', $id)
            ->firstOrFail();

        $quantity = (float) $request['inventory_quantity'];

        if (
            $request['type'] === 'salida' &&
            $quantity > $inventory->quantity
        ) {

            alert()->error('No hay suficiente stock.');

            return back();
        }

        $movement = new InventoryMovement();

        if ($request['type'] === 'salida') {

            if($request['inventory_sale_id']) {
                $movement->sale_id = $request['inventory_sale_id'];
            }

            $movement->name = $request['inventory_name'];
        }

        $movement->product_id = $inventory->product_id;
        $movement->type = $request['type'];
        $movement->date = $request['inventory_date'];
        $movement->quantity = $quantity;

        $inventory->movements()->save($movement);

        if ($request['type'] === 'entrada') {

            $inventory->quantity += $quantity;

        } elseif ($request['type'] === 'salida') {

            $inventory->quantity -= $quantity;
        }

        $inventory->total_value =
            $inventory->product->costo_venta *
            $inventory->quantity;

        $inventory->save();

        alert('Se ha actualizado un inventario.');

        return response('', 204, [
            'Redirect-To' => url('admin/inventario/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('create.inventories'), 403);

        $inventory = Inventory::find($id);
        $inventory->delete();
        
        return response('', 204);

    }
}
