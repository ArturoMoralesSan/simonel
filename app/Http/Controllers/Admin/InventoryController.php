<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\SaleProduct;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Auth;

class InventoryController extends Controller
{
    public function getByClient(Request $request)
    {
        $clientId = $request->query('client'); // ?client=ID

        if (!$clientId) {
            return response()->json(['error' => 'Client ID requerido'], 400);
        }

        $inventory = Inventory::with('product.manufactured')
            ->where('user_id', $clientId)
            ->get();

        $movements = InventoryMovement::with('inventory.product.manufactured')
            ->whereHas('inventory', function ($q) use ($clientId) {
                $q->where('user_id', $clientId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsEntradas = $movements->where('type', 'entrada')->values();
        $movementsSalidas = $movements->where('type', 'salida')->values();
        $movementsMermas = $movements->where('type', 'merma')->values();

        $pendingProducts = SaleProduct::selectRaw("
            sale_products.id,
            sale_products.product_id,
            CONCAT(
                manufactured_products.name,
                ' (Venta #',
                sales.id,
                ' - ',
                sale_products.quantity,
                ')'
            ) as name
        ")
        ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
        ->join('products', 'products.id', '=', 'sale_products.product_id')
        ->join('manufactured_products', 'manufactured_products.id', '=', 'products.manufactured_product_id')
        ->where('sales.user_id', $clientId)
        ->whereIn('sales.status', ['paid', 'credit'])        
        ->orderBy('manufactured_products.name')
        ->pluck('name', 'product_id');

        $pendingProductsData = SaleProduct::selectRaw("
            sale_products.sale_id,
            sale_products.product_id,
            sale_products.quantity
        ")
        ->join('sales', 'sales.id', '=', 'sale_products.sale_id')
        ->where('sales.user_id', $clientId)
        ->whereIn('sales.status', ['paid', 'credit'])
        ->get()
        ->keyBy('product_id');
        $customer = User::with('customer')->findOrFail($clientId);
        return response()->json([
            'inventory' => $inventory,
            'movementsEntradas' => $movementsEntradas,
            'movementsSalidas' => $movementsSalidas,
            'movementsMermas' => $movementsMermas,
            'pendingProducts' => $pendingProducts,
            'pendingProductsData' => $pendingProductsData,
            'customer_type' => $customer->customer->customer_type,
        ]);
    }


    public function storeMovement(InventoryRequest $request)
    {
        DB::transaction(function () use ($request) {

            for ($i = 1; $i <= $request->product_count; $i++) {

                $inventory = Inventory::firstOrCreate(
                    [
                        'user_id' => $request['client_id'],
                        'product_id' => $request['inventory' . $i . '_product_id'],
                    ],
                    [
                        'inventory_type' => 'Externo',
                        'tag' => 'Sin etiqueta',
                        'quantity' => 0,
                        'quantity_min' => 0,
                        'total_value' => 0,
                    ]
                );

                $qty = (float) $request['inventory' . $i . '_quantity'];

                if (in_array($request['type'], ['salida', 'merma']) && $inventory->quantity < $qty) {
                   throw ValidationException::withMessages([
                        'inventory' . $i . '_quantity' => [
                            'inventario insuficiente'
                        ]
                    ]);
                }

                $movement = new InventoryMovement();

                if ($request['type'] === 'salida') {
                    $movement->sale_id = $request['inventory' . $i . '_sale_id'] ?? null;
                    $movement->name = $request['inventory' . $i . '_name'] ?? null;
                }

                $movement->product_id = $request['inventory' . $i . '_product_id'];
                $movement->type = $request['type'];
                $movement->date = $request['inventory' . $i . '_date'];
                $movement->quantity = $qty;

                $inventory->movements()->save($movement);

                // Actualizar venta cuando entra a cámara
                if ($request['type'] === 'entrada' && !empty($request['inventory' . $i . '_sale_id'])) {
                    $sale = Sale::find($request['inventory' . $i . '_sale_id']);

                    if ($sale && $sale->status !== 'assortment') {
                        $sale->status = 'assortment';
                        $sale->save();
                    }
                }

                switch ($request['type']) {

                    case 'entrada':
                        $inventory->quantity += $qty;
                        break;

                    case 'salida':
                    case 'merma':
                        $inventory->quantity -= $qty;
                        break;
                }

                $inventory->inventory_type = 'Externo';
                $inventory->tag = 'Sin etiqueta';
                $inventory->save();
            }
        });

        return response('', 204, [
            'Redirect-To' => url('admin/inventario-clientes/')
        ]);
    }

    
    public function index()
    {
        abort_unless(
            Gate::allows('view.inventories') || Gate::allows('create.inventories'),
            403
        );

        $search = request('search');

        $query = User::whereHas('inventories')
        ->when(!Auth::user()->isSuperAdmin() || Auth::user()->isAdmin(), function ($query) {
            $query->whereHas('customer', function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('seller_id', Auth::id());
                });
            });
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $paginatedClients = $query->paginate(10)->appends(request()->all());

        $inventoriesItems = collect($paginatedClients->items())->transform(function ($user) {

            $inventories = $user->inventories()
                ->with('product.manufactured')
                ->get();

            return [
                'client' => $user,
                'count' => $inventories->count(),
                'inventories' => $inventories
                    ->map(fn ($inv) => $inv->product->manufactured->name)
                    ->implode(', '),
            ];
        });

        $links = $paginatedClients->links('layout.pagination');

        return view('admin.inventario.index', compact(
            'inventoriesItems',
            'links'
        ));
    }

    
    public function details($id)
    {
        $user = User::with('customer')->findOrFail($id);
        $clienteTipo = $user->customer?->customer_type ?? 'minorista'; // o el campo real que uses

        $inventory = Inventory::with('product.manufactured')
            ->where('user_id', $id)
            ->get();

        $movements = InventoryMovement::with('inventory.product.manufactured')
            ->whereHas('inventory', function ($q) use ($id) {
                $q->where('user_id', $id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsEntradas = $movements->where('type', 'entrada')->values();
        $movementsSalidas = $movements->where('type', 'salida')->values();
        $movementsMermas = $movements->where('type', 'merma')->values();


        return view('admin.inventario.details', compact(
            'inventory',
            'movementsEntradas',
            'movementsSalidas',
            'movementsMermas',
            'user',
            'clienteTipo'
        ));
    }

    public function create()
    {
        abort_unless(
            Gate::allows('view.inventories') || Gate::allows('edit.inventories'),
            403
        );

        $users = Customer::when(!Auth::user()->isSuperAdmin() || Auth::user()->isAdmin(), function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('seller_id', Auth::id());
            });
        })
        ->selectRaw("
            CONCAT(trade_name, ' (', business_name, ')') as full_name,
            user_id
        ")
        ->pluck('full_name', 'user_id');

        return view('admin.inventario.crear', compact(
         'users'
        ));
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


    public function inventory()
    {
        abort_unless(Gate::allows('view.warehouses'),403);

        $warehouseTypes = Warehouse::where('active', 1)
            ->select('warehouse_type')
            ->distinct()
            ->orderBy('warehouse_type')
            ->pluck('warehouse_type', 'warehouse_type');

        return view('admin.inventarioalmacen.crear', compact(
            'warehouseTypes'
        ));
    }

    public function inventoryWarehouse(Request $request)
    {
        $request->validate([
            'warehouse_type' => ['required'],
        ]);

        $warehouses = Warehouse::where('active', 1)
            ->where('warehouse_type', $request->warehouse_type)
            ->with([
                'productLots.product.manufactured',
                'lots.material',
            ])
            ->get();

        $inventory = collect();

        foreach ($warehouses as $warehouse) {

            /*
            |--------------------------------------------------------------------------
            | PRODUCTOS TERMINADOS
            |--------------------------------------------------------------------------
            */

            foreach ($warehouse->productLots as $lot) {

                if (
                    !$lot->product ||
                    $lot->status !== 'Disponible' ||
                    (float) $lot->available_quantity <= 0
                ) {
                    continue;
                }

                $itemId = $lot->product_id;
                $key = 'product_' . $itemId;

                if (!$inventory->has($key)) {

                    $inventory->put($key, [
                        'item_id' => $itemId,
                        'type' => 'product',

                        'name' => optional(
                            $lot->product->manufactured
                        )->name ?? 'Producto eliminado',

                        'description' => optional(
                            $lot->product->manufactured
                        )->description ?? '',

                        'quantity' => 0,

                        'warehouses' => [],
                    ]);
                }

                $item = $inventory->get($key);

                $quantity = (float) $lot->available_quantity;

                $item['quantity'] += $quantity;

                /*
                |--------------------------------------------------------------------------
                | Buscar almacén
                |--------------------------------------------------------------------------
                */

                $warehouseIndex = collect($item['warehouses'])
                    ->search(function ($itemWarehouse) use ($warehouse) {
                        return $itemWarehouse['id'] == $warehouse->id;
                    });

                /*
                |--------------------------------------------------------------------------
                | Datos del lote
                |--------------------------------------------------------------------------
                */

                $lotData = [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'quantity' => $quantity,
                    'expiration_date' => $lot->expiration_date,

                    'is_expired' => $lot->expiration_date
                        ? $lot->expiration_date < now()->toDateString()
                        : false,
                ];

                if ($warehouseIndex !== false) {

                    $item['warehouses'][$warehouseIndex]['quantity']
                        += $quantity;

                    $item['warehouses'][$warehouseIndex]['lots'][] = $lotData;

                } else {

                    $item['warehouses'][] = [
                        'id' => $warehouse->id,
                        'name' => $warehouse->name,
                        'quantity' => $quantity,

                        'lots' => [
                            $lotData
                        ],
                    ];
                }

                $inventory->put($key, $item);
            }


            /*
            |--------------------------------------------------------------------------
            | MATERIAS PRIMAS
            |--------------------------------------------------------------------------
            */

            foreach ($warehouse->lots as $lot) {

                if (
                    !$lot->material ||
                    $lot->status !== 'Disponible' ||
                    (float) $lot->available_quantity <= 0
                ) {
                    continue;
                }

                $itemId = $lot->raw_material_id;
                $key = 'raw_' . $itemId;

                if (!$inventory->has($key)) {

                    $inventory->put($key, [
                        'item_id' => $itemId,
                        'type' => 'raw_material',

                        'name' => $lot->material->name,

                        'description' =>
                            $lot->material->description ?? '',

                        'quantity' => 0,

                        'warehouses' => [],
                    ]);
                }

                $item = $inventory->get($key);

                $quantity = (float) $lot->available_quantity;

                $item['quantity'] += $quantity;

                /*
                |--------------------------------------------------------------------------
                | Buscar almacén
                |--------------------------------------------------------------------------
                */

                $warehouseIndex = collect($item['warehouses'])
                    ->search(function ($itemWarehouse) use ($warehouse) {
                        return $itemWarehouse['id'] == $warehouse->id;
                    });

                /*
                |--------------------------------------------------------------------------
                | Datos del lote
                |--------------------------------------------------------------------------
                */

                $lotData = [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'quantity' => $quantity,
                    'expiration_date' => $lot->expiration_date,

                    'is_expired' => $lot->expiration_date
                        ? $lot->expiration_date < now()->toDateString()
                        : false,
                ];

                if ($warehouseIndex !== false) {

                    $item['warehouses'][$warehouseIndex]['quantity']
                        += $quantity;

                    $item['warehouses'][$warehouseIndex]['lots'][] = $lotData;

                } else {

                    $item['warehouses'][] = [
                        'id' => $warehouse->id,
                        'name' => $warehouse->name,
                        'quantity' => $quantity,

                        'lots' => [
                            $lotData
                        ],
                    ];
                }

                $inventory->put($key, $item);
            }
        }

        return response()->json([
            'inventory' => $inventory
                ->sortBy('name')
                ->values(),
        ]);
    }
}
