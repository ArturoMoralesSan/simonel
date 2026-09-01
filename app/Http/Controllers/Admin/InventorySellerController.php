<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\SellerInventoryAssignment;
use App\Models\SellerInventoryAssignmentLot;
use App\Models\SellerInventoryMovement;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InventorySellerController extends Controller
{
    public function index()
    {
        abort_unless(
            Gate::allows('view.inventories') ||
            Gate::allows('create.inventories'),
            403
        );

        $search = request('search');

        $sellerRole = Role::where('key_name', 'vendedores')->firstOrFail();

        $sellerIds = SellerInventoryAssignment::query()
            ->select('seller_id')
            ->distinct();

        $query = User::query()
            ->where('role_id', $sellerRole->id)
            ->whereIn('id', $sellerIds)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            });

        $paginatedSellers = $query
            ->paginate(10)
            ->appends(request()->all());

        $sellersItems = collect($paginatedSellers->items())
            ->transform(function ($user) {

                $assignments = SellerInventoryAssignment::where(
                    'seller_id',
                    $user->id
                )
                ->with([
                    'lots.productLot.product.manufactured'
                ])
                ->latest('assignment_date')
                ->get();

                $products = collect();

                foreach ($assignments as $assignment) {

                    foreach ($assignment->lots as $lot) {

                        $product = $lot->productLot?->product;

                        if (!$product) {
                            continue;
                        }

                        $productName =
                            $product->product?->manufactured?->name
                            ?? $product->product?->name
                            ?? 'Producto';

                        $products->push($productName);
                    }
                }

                return [
                    'seller' => $user,
                    'count' => $assignments->count(),
                    'products' => $products
                        ->unique()
                        ->implode(', '),
                ];
            });

        $links = $paginatedSellers->links('layout.pagination');

        return view('admin.inventariovendedor.index', compact(
            'sellersItems',
            'links'
        ));
    }

    /**
     * Formulario para asignar inventario a un vendedor.
     */
    public function create()
    {
        abort_unless(Gate::allows('view.inventories'), 403);

        $sellerRole = Role::where('key_name', 'vendedores')->firstOrFail();

        $sellers = User::where('role_id', $sellerRole->id)
            ->selectRaw("
                CONCAT(name, ' ', COALESCE(last_name, '')) as full_name,
                id
            ")
            ->pluck('full_name', 'id');

        $productLots = ProductLot::with([
            'product.manufactured',
        ])
        ->where('available_quantity', '>', 0)
        ->get();

        $productLotOptions = [];

        foreach ($productLots as $lot) {

            $productName = optional($lot->product->manufactured)->name
                ?? optional($lot->product)->name
                ?? 'Producto';

            $productLotOptions[$lot->id] =
                $productName .
                ' - Lote #' . $lot->id .
                ' (' . number_format($lot->available_quantity, 2) . ' disponibles)';
        }

        return view(
            'admin.inventariovendedor.crear',
            compact(
                'sellers',
                'productLotOptions'
            )
        );
    }


    public function getBySeller(Request $request)
    {
        $sellerId = $request->query('seller');

        if (!$sellerId) {
            return response()->json([
                'error' => 'Seller ID requerido'
            ], 400);
        }

        $seller = User::findOrFail($sellerId);

        /*
        * ============================================================
        * ASIGNACIÓN ABIERTA DEL DÍA
        * ============================================================
        */
        $assignment = SellerInventoryAssignment::with([
            'seller',
            'lots.productLot.product.manufactured',
            'lots.productLot.product',
        ])
            ->where('seller_id', $sellerId)
            ->whereDate('assignment_date', Carbon::today())
            ->where('status', 'open')
            ->first();

        /*
        * ============================================================
        * INVENTARIO ACTUAL
        * ============================================================
        */
        $inventory = [];

        if ($assignment) {
            $inventory = $assignment->lots
                ->filter(function ($lot) {
                    return (float) $lot->available_quantity > 0;
                })
                ->values();
        }

        /*
        * ============================================================
        * HISTORIAL DE MOVIMIENTOS
        * ============================================================
        *
        * IMPORTANTE:
        * Aquí NO filtramos por assignment_id.
        *
        * Traemos todos los movimientos del vendedor:
        *
        * assignment
        * sale
        * return
        *
        * incluyendo asignaciones ya cerradas.
        */
        $movements = SellerInventoryMovement::with([
            'assignment',
            'assignmentLot',
            'productLot.product.manufactured',
            'productLot.product',
            'seller',
        ])
            ->where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        /*
        * Movimientos separados por tipo.
        */
        $movementsAssignments = $movements
            ->where('type', 'assignment')
            ->values();

        $movementsSales = $movements
            ->where('type', 'sale')
            ->values();

        $movementsReturns = $movements
            ->where('type', 'return')
            ->values();

        /*
        * ============================================================
        * LOTES DISPONIBLES PARA NUEVAS ASIGNACIONES
        * ============================================================
        */
        $productLots = ProductLot::with([
            'product.manufactured',
            'product',
        ])
            ->where('available_quantity', '>', 0)
            ->get();

        $productLotOptions = [];

        foreach ($productLots as $lot) {

            $productName =
                optional($lot->product?->manufactured)->name
                ?? optional($lot->product)->name
                ?? 'Producto';

            $productLotOptions[$lot->id] =
                $productName .
                ' - Lote #' . $lot->id .
                ' (' .
                number_format(
                    (float) $lot->available_quantity,
                    2
                ) .
                ' disponibles)';
        }

        /*
        * ============================================================
        * VENTAS DEL DÍA
        * ============================================================
        */
        $sales = Sale::where('user_id', $sellerId)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['paid', 'credit'])
            ->with([
                'products.product.manufactured',
            ])
            ->latest()
            ->get();

        /*
        * ============================================================
        * RESPUESTA
        * ============================================================
        */
        return response()->json([
            'seller' => $seller,

            // Solamente la asignación abierta de HOY
            'assignment' => $assignment,

            // Inventario actual
            'inventory' => $inventory,

            // TODO el historial
            'movements' => $movements,

            // Historial separado
            'movementsAssignments' => $movementsAssignments,
            'movementsSales' => $movementsSales,
            'movementsReturns' => $movementsReturns,

            // Lotes disponibles
            'productLotOptions' => $productLotOptions,

            // Ventas del día
            'sales' => $sales,
        ]);
    }





    public function storeMovement(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'product_count' => 'required|integer|min:1',
        ]);

        /*
        * --------------------------------------------------------------------------
        * VALIDAR CADA PRODUCTO
        * --------------------------------------------------------------------------
        */

        for ($i = 1; $i <= $request->product_count; $i++) {

            $request->validate([
                "assignment{$i}_product_lot_id" => [
                    'required',
                    'exists:product_lots,id',
                ],

                "assignment{$i}_quantity" => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                "assignment{$i}_date" => [
                    'required',
                    'date',
                ],
            ]);

            /*
            * ----------------------------------------------------------------------
            * LA FECHA DEBE SER HOY
            * ----------------------------------------------------------------------
            */

            if (!Carbon::parse(
                $request->input("assignment{$i}_date")
            )->isToday()) {

                throw ValidationException::withMessages([
                    "assignment{$i}_date" => [
                        'La fecha de asignación debe ser la fecha de hoy.'
                    ]
                ]);
            }
        }

        /*
        * --------------------------------------------------------------------------
        * TRANSACCIÓN
        * --------------------------------------------------------------------------
        */

        DB::transaction(function () use ($request) {

            $sellerId = $request->seller_id;

            /*
            * ----------------------------------------------------------------------
            * BUSCAR ASIGNACIÓN ABIERTA DEL DÍA
            * ----------------------------------------------------------------------
            */

            $assignment = SellerInventoryAssignment::where(
                'seller_id',
                $sellerId
            )
            ->whereDate(
                'assignment_date',
                Carbon::today()
            )
            ->where(
                'status',
                'open'
            )
            ->lockForUpdate()
            ->first();

            /*
            * ----------------------------------------------------------------------
            * CREAR ASIGNACIÓN SI NO EXISTE
            * ----------------------------------------------------------------------
            */

            if (!$assignment) {

                $assignment = SellerInventoryAssignment::create([
                    'seller_id' => $sellerId,
                    'assignment_date' => Carbon::today(),
                    'status' => 'open',
                ]);
            }

            /*
            * ----------------------------------------------------------------------
            * PROCESAR PRODUCTOS
            * ----------------------------------------------------------------------
            */

            for ($i = 1; $i <= $request->product_count; $i++) {

                $productLotId = $request->input(
                    "assignment{$i}_product_lot_id"
                );

                $quantity = (float) $request->input(
                    "assignment{$i}_quantity"
                );

                /*
                * ------------------------------------------------------------------
                * OBTENER LOTE
                * ------------------------------------------------------------------
                */

                $productLot = ProductLot::lockForUpdate()
                    ->findOrFail($productLotId);

                /*
                * ------------------------------------------------------------------
                * VALIDAR EXISTENCIA
                * ------------------------------------------------------------------
                */

                if (
                    (float) $productLot->available_quantity
                    < $quantity
                ) {

                    throw ValidationException::withMessages([
                        "assignment{$i}_quantity" => [
                            'El lote no tiene suficiente cantidad disponible. ' .
                            'Disponible: ' .
                            number_format(
                                $productLot->available_quantity,
                                2
                            ) .
                            ', solicitado: ' .
                            number_format(
                                $quantity,
                                2
                            ) .
                            '.'
                        ]
                    ]);
                }

                /*
                * ------------------------------------------------------------------
                * BUSCAR DETALLE DEL LOTE YA ASIGNADO
                * ------------------------------------------------------------------
                */

                $assignmentLot =
                    SellerInventoryAssignmentLot::where(
                        'assignment_id',
                        $assignment->id
                    )
                    ->where(
                        'product_lot_id',
                        $productLot->id
                    )
                    ->lockForUpdate()
                    ->first();

                /*
                * ------------------------------------------------------------------
                * ACTUALIZAR / CREAR DETALLE
                * ------------------------------------------------------------------
                */

                if ($assignmentLot) {
                    $assignmentLot->quantity += $quantity;
                    $assignmentLot->available_quantity += $quantity;
                    $assignmentLot->save();
                } else {

                    $assignmentLot =
                        SellerInventoryAssignmentLot::create([
                            'assignment_id' => $assignment->id,
                            'product_lot_id' => $productLot->id,
                            'quantity' => $quantity,
                            'available_quantity' => $quantity,
                        ]);
                }

                /*
                * ------------------------------------------------------------------
                * DESCONTAR DEL LOTE FÍSICO
                * ------------------------------------------------------------------
                */

                $productLot->available_quantity -= $quantity;

                /*
                * ------------------------------------------------------------------
                * SI EL LOTE SE TERMINÓ
                * ------------------------------------------------------------------
                */

                if ($productLot->available_quantity <= 0) {
                    $productLot->available_quantity = 0;
                    $productLot->status = 'Agotado';
                }

                $productLot->save();

                /*
                * ------------------------------------------------------------------
                * REGISTRAR MOVIMIENTO
                * ------------------------------------------------------------------
                */

                SellerInventoryMovement::create([
                    'seller_id' => $sellerId,
                    'assignment_id' => $assignment->id,
                    'product_lot_id' => $productLot->id,
                    'type' => 'assignment',
                    'quantity' => $quantity,
                ]);
            }
        });

        return response('', 204, [
            'Redirect-To' => url(
                'admin/inventario-vendedores'
            )
        ]);
    }





    /**
     * Detalle de un vendedor.
     */
    public function details($id)
    {
        $seller = User::findOrFail($id);


        $assignments =
            SellerInventoryAssignment::with([
                'lots.product.manufactured',
                'lots.product',
                'movements.productLot.product.manufactured'
            ])
            ->where('seller_id', $id)
            ->orderBy('assignment_date', 'desc')
            ->get();


        return view(
            'admin.inventario-vendedores.details',
            compact(
                'seller',
                'assignments'
            )
        );
    }






    /**
     * Actualizar un movimiento.
     *
     * Normalmente no recomiendo modificar movimientos históricos.
     * Si necesitas correcciones, es mejor generar un movimiento
     * inverso.
     */
    public function updateMovement(Request $request, $id)
    {
        $movement =
            SellerInventoryMovement::findOrFail($id);


        $request->validate([
            'quantity' => 'required|numeric|min:0.0001',
        ]);


        $movement->update([
            'quantity' => $request->quantity,
        ]);


        return response('', 204);
    }


    
    public function close($id)
    {
        DB::transaction(function () use ($id) {

            $assignment = SellerInventoryAssignment::with('lots')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($assignment->status === 'closed') {
                return;
            }

            foreach ($assignment->lots as $assignmentLot) {

                $quantity = (float) $assignmentLot->available_quantity;

                if ($quantity <= 0) {
                    continue;
                }

                // El sobrante vuelve al lote físico
                $productLot = ProductLot::lockForUpdate()
                    ->findOrFail($assignmentLot->product_lot_id);

                $productLot->available_quantity += $quantity;
                $productLot->save();

                // Registramos la devolución
                SellerInventoryMovement::create([
                    'seller_id' => $assignment->seller_id,
                    'assignment_id' => $assignment->id,
                    'product_lot_id' => $assignmentLot->product_lot_id,
                    'type' => 'return',
                    'quantity' => $quantity,
                ]);

                // Ya no queda producto disponible para el vendedor
                $assignmentLot->available_quantity = 0;
                $assignmentLot->save();
            }

            // Cerramos la asignación
            $assignment->status = 'closed';
            $assignment->closed_at = now();
            $assignment->save();
        });

        return response()->noContent();
    }


    /**
     * Eliminar una asignación.
     */
    public function delete($id)
    {
        abort_unless(
            Gate::allows('view.inventories') ||
            Gate::allows('create.inventories'),
            403
        );


        $assignment =
            SellerInventoryAssignment::findOrFail($id);


        /*
         * No permitir eliminar una asignación que ya tenga
         * movimientos, porque romperíamos el historial.
         */
        if (
            SellerInventoryMovement::where(
                'assignment_id',
                $assignment->id
            )->exists()
        ) {

            throw ValidationException::withMessages([
                'assignment' => [
                    'No se puede eliminar una asignación que ya tiene movimientos.'
                ]
            ]);
        }


        $assignment->delete();


        return response('', 204);
    }
}