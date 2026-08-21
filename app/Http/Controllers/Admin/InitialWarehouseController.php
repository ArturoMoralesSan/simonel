<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitialWarehouseRequest;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Warehouse;
use App\Models\RawMaterialLot;
use App\Models\ProductLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InitialWarehouseController extends Controller
{
    /**
     * Mostrar formulario de inventario inicial
     */
    public function create()
    {
        abort_unless(
            Gate::allows('view.roles') ||
            Gate::allows('create.roles'),
            403
        );

        $materials = RawMaterial::orderBy('name')->pluck('name', 'id');

        $products = Product::with('manufactured')
            ->get()
            ->mapWithKeys(function ($product) {
                return [
                    $product->id => optional($product->manufactured)->name
                        ?? 'Producto #' . $product->id
                ];
            });

        $warehouses = Warehouse::where('active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.inventarioinicial.crear', compact(
            'materials',
            'products',
            'warehouses'
        ));
    }

    public function save(InitialWarehouseRequest $request)
    {
        abort_unless(
            Gate::allows('view.roles') ||
            Gate::allows('create.roles'),
            403
        );

        DB::transaction(function () use ($request) {

            for ($i = 1; $i <= $request->item_count; $i++) {

                $type = $request->input("item{$i}_type");

                $warehouseId = $request->input(
                    "item{$i}_warehouse_id"
                );

                $quantity = (float) $request->input(
                    "item{$i}_quantity"
                );

                /*
                * Este es el costo unitario que el usuario
                * está asignando al inventario inicial.
                */
                $unitCost = (float) $request->input(
                    "item{$i}_unit_cost"
                );

                if (
                    !$type ||
                    !$warehouseId ||
                    $quantity <= 0 ||
                    $unitCost < 0
                ) {
                    continue;
                }

                /*
                * ==========================================================
                * COSTO TOTAL DEL LOTE
                * ==========================================================
                */

                $totalCost = $quantity * $unitCost;

                /*
                * ==========================================================
                * NÚMERO DE LOTE
                * ==========================================================
                */

                $lotNumber =
                    'INICIAL-' .
                    now()->format('YmdHisv') .
                    '-' .
                    $i;

                /*
                * ==========================================================
                * MATERIA PRIMA
                * ==========================================================
                */

                if ($type === 'raw_material') {

                    $rawMaterialId = $request->input(
                        "item{$i}_raw_material_id"
                    );

                    if (!$rawMaterialId) {
                        continue;
                    }

                    /*
                    * Buscar materia prima
                    */
                    $rawMaterial = RawMaterial::findOrFail(
                        $rawMaterialId
                    );

                    /*
                    * ======================================================
                    * ACTUALIZAR COSTO DE LA MATERIA PRIMA
                    * ======================================================
                    *
                    * Como es inventario inicial y no existe una compra
                    * que determine el costo, el costo introducido por
                    * el usuario se convierte en el costo actual de
                    * la materia prima.
                    */

                    $rawMaterial->cost = $unitCost;
                    $rawMaterial->save();

                    /*
                    * ======================================================
                    * CREAR LOTE DE MATERIA PRIMA
                    * ======================================================
                    */

                    $lot = new RawMaterialLot();

                    $lot->raw_material_id = $rawMaterialId;
                    $lot->warehouse_id = $warehouseId;

                    /*
                    * El inventario inicial no proviene de una compra.
                    */
                    $lot->purchase_id = null;
                    $lot->supplier_id = null;
                    $lot->supplier_lot = null;

                    $lot->lot_number = $lotNumber;

                    $lot->entry_date = now()->toDateString();

                    $lot->expiration_date = $request->input(
                        "item{$i}_expiration_date"
                    );

                    $lot->initial_quantity = $quantity;
                    $lot->available_quantity = $quantity;

                    /*
                    * raw_material_lots.cost almacena
                    * el COSTO TOTAL del lote.
                    *
                    * Ejemplo:
                    * 100 kg × $25 = $2,500
                    */
                    $lot->cost = $totalCost;

                    $lot->status = 'Disponible';

                    $lot->save();

                    continue;
                }

                /*
                * ==========================================================
                * PRODUCTO TERMINADO
                * ==========================================================
                */

                if ($type === 'product') {

                    $productId = $request->input(
                        "item{$i}_product_id"
                    );

                    if (!$productId) {
                        continue;
                    }

                    /*
                    * Crear lote de producto terminado
                    */

                    $lot = new ProductLot();

                    $lot->product_id = $productId;

                    /*
                    * El inventario inicial no pertenece
                    * a una orden de producción.
                    */
                    $lot->production_order_id = null;

                    $lot->warehouse_id = $warehouseId;

                    $lot->lot_number = $lotNumber;

                    $lot->production_date = now()->toDateString();

                    $lot->expiration_date = $request->input(
                        "item{$i}_expiration_date"
                    );

                    $lot->initial_quantity = $quantity;
                    $lot->available_quantity = $quantity;

                    /*
                    * Costo unitario del producto.
                    */
                    $lot->cost_per_unit = $unitCost;

                    /*
                    * Costo total del lote.
                    */
                    $lot->total_cost = $totalCost;

                    $lot->status = 'Disponible';
                    $lot->active = 1;

                    $lot->save();
                }
            }
        });

        alert(
            'El inventario inicial se ha registrado correctamente.'
        );

        return response('', 204, [
            'Redirect-To' => url('admin/inventario-inicial')
        ]);
    }
}