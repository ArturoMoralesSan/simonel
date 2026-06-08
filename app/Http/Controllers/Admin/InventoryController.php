<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function getByClient(Request $request)
    {
        $clientId = $request->query('client'); // ?client=ID

        if (!$clientId) {
            return response()->json(['error' => 'Client ID requerido'], 400);
        }

        $inventory = Inventory::with('product')
            ->where('user_id', $clientId)
            ->get();

        $movements = InventoryMovement::with('inventory.product')
            ->whereHas('inventory', function ($q) use ($clientId) {
                $q->where('user_id', $clientId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsEntradas = $movements->where('type', 'entrada')->values();
        $movementsSalidas = $movements->where('type', 'salida')->values();

        return response()->json([
            'inventory' => $inventory,
            'movementsEntradas' => $movementsEntradas,
            'movementsSalidas' => $movementsSalidas,
        ]);
    }


    public function storeMovement(InventoryRequest $request)
    {

        for($i = 1; $i <= $request->product_count; $i++){
            $inventory = Inventory::firstOrCreate(
                [
                    'user_id'    => $request['client_id'],
                    'product_id'=> $request['inventory' . $i . '_product_id'],
                ],
                [
                    'inventory_type' => 'Externo',
                    'tag' => 'Sin etiqueta',
                    'quantity' => 0,
                    'quantity_min' => 0,
                    'total_value' => 0,
                ]
            );

            $movement = new InventoryMovement();

            if ($request['type'] === 'salida') {
                $movement->sale_id = $request['inventory' . $i . '_sale_id'];
                $movement->name = $request['inventory' . $i . '_name'];
            }
            $movement->product_id = $request['inventory' . $i . '_product_id'];
            $movement->type = $request['type'];
            $movement->date  = $request['inventory' . $i . '_date'];
            $movement->quantity = $request['inventory' . $i . '_quantity'];

            $inventory->movements()->save($movement);

            // Actualizar inventario
            if ($request['type'] === 'entrada') {
                $inventory->quantity += $request['inventory' . $i . '_quantity'];
            } else {
                $inventory->quantity -= $request['inventory' . $i . '_quantity'];
            }

            $inventory->inventory_type = 'Externo';
            $inventory->tag = 'Sin etiqueta';
            $inventory->save();
        }

        return response('', 204, [
            'Redirect-To' => url('admin/inventario-clientes/')
        ]);
    }

    public function updateMovement(Request $request, $id)
    {
        // Validar datos
        $validated = $request->validate([
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'product_id' => 'required|exists:product_templates,id',
            'sale_id' => 'max:20', // Para salidas
            'name' => 'max:120', // Para salidas
        ]);

        $movement = InventoryMovement::findOrFail($id);
        $inventory = $movement->inventory;

        if (!$inventory) {
            return response()->json(['error' => 'Inventario no encontrado'], 404);
        }

        // Revertir el efecto anterior
        if ($movement->type === 'entrada') {
            $inventory->quantity -= $movement->quantity;
        } else { // salida
            $inventory->quantity += $movement->quantity;
        }

        // Actualizar datos del movimiento
        $movement->date = $validated['date'];
        $movement->quantity = $validated['quantity'];
        $movement->product_id = $validated['product_id'];

        if ($movement->type === 'salida') {
            $movement->sale_id = $validated['sale_id'] ?? $movement->sale_id;
            $movement->name = $validated['name'] ?? $movement->name;
        }

        // Aplicar nuevamente el efecto
        if ($movement->type === 'entrada') {
            $inventory->quantity += $validated['quantity'];
        } else { // salida
            $inventory->quantity -= $validated['quantity'];
        }

        $inventory->product_id = $validated['product_id'];

        $inventory->save();
        $movement->save();

        return response()->json([
            'message' => 'Movimiento actualizado correctamente',
            'movement' => $movement,
            'inventory' => $inventory,
        ]);
    }

    
    public function index()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('create.inventories'), 403);

        $search = request('search');

        // 1. Usuarios que tienen inventarios
        $query = User::whereHas('inventories');

        // 2. Búsqueda
        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        // 3. Paginar clientes/usuarios
        $paginatedClients = $query->paginate(10);

        // 4. Formatear items
        $inventoriesItems = $paginatedClients->map(function ($user) {
            $inventories = $user->inventories()->with('product')->get();

            return [
                'client' => $user,
                'count' => $inventories->count(),
                'inventories' => $inventories->pluck('product.name')->implode(', '),
            ];
        });

        $links = $paginatedClients->links('layout.pagination');

        return view('admin.inventario.index', compact('inventoriesItems', 'links'));
    }

    
    public function details($id)
    {
        $user = User::find($id);
        $inventory = Inventory::with('product')
            ->where('user_id', $id)
            ->get();

        $movements = InventoryMovement::with('inventory.product')
            ->whereHas('inventory', function ($q) use ($id) {
                $q->where('user_id', $id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsEntradas = $movements->where('type', 'entrada')->values();
        $movementsSalidas = $movements->where('type', 'salida')->values();

        return view('admin.inventario.details', compact('inventory', 'movementsEntradas','movementsSalidas', 'user' ));
    }

    public function create()
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
            $labels = [
                'entradas' => 'Entradas',
                'salidas'  => 'Salidas',
                'Resumen' => 'Resumen',
            ];

        $users = Customer::selectRaw("CONCAT(trade_name, ' (', business_name,')') as full_name, user_id")
            ->pluck('full_name', 'user_id');
        $products = Product::orderBy('name', 'ASC')->get();


        return view('admin.inventario.crear', compact('users', 'labels', 'products'));
    }

    public function save(InventoryRequest $request)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $product = Product::find($request->product_id);
        $total = $product->costo_venta * $request->quantity;
        $inventory = new Inventory;
        $inventory->product_id   = $request->product_id;
        $inventory->quantity_min = $request->quantity_min;
        $inventory->quantity     = $request->quantity;
        $inventory->total        = $total;
        $inventory->save();

        Inventory::checkStock($inventory);


        alert('Se ha agregado un elemento al inventario.');

        return response('', 204, [
            'Redirect-To' => url('admin/inventario/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $inventory = Inventory::find($id);
        $products = Product::pluck('name', 'id');
        return view('admin.inventario.editar', compact('inventory', 'products'));
    }


    public function update(InventoryRequest $request, $id)
    {
        abort_unless(Gate::allows('view.inventories') || Gate::allows('edit.inventories'), 403);
        $product = Product::find($request->product_id);
        $total = $product->costo_venta * $request->quantity;
        $inventory = Inventory::find($id);
        $inventory->product_id   = $request->product_id;
        $inventory->quantity_min = $request->quantity_min;
        $inventory->quantity     = $request->quantity;
        $inventory->total        = $total;
        $inventory->save();

        Inventory::checkStock($inventory);


        alert('Se ha actualizado un elemento en el inventario.');

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
