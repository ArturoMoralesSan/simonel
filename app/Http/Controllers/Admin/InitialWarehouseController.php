<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitialWarehouseRequest;
use App\Models\ManufacturedProduct;
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

        $materials = RawMaterial::orderBy('name')
            ->pluck('name', 'id');

        $products = ManufacturedProduct::orderBy('name')
            ->pluck('name', 'id');

        $warehouses = Warehouse::where('active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view(
            'admin.inventarioinicial.crear',
            compact(
                'materials',
                'products',
                'warehouses'
            )
        );
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
                |--------------------------------------------------------------------------
                | COSTO UNITARIO
                |--------------------------------------------------------------------------
                |
                | Para materia prima se mantiene el valor actual.
                | Para producto terminado utilizaremos manufacturing_cost
                | directamente más adelante.
                |
                */

                $unitCost = (float) $request->input(
                    "item{$i}_public_price"
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
                |--------------------------------------------------------------------------
                | COSTO TOTAL DEL LOTE
                |--------------------------------------------------------------------------
                */

                $totalCost = $quantity * $unitCost;

                /*
                |--------------------------------------------------------------------------
                | NÚMERO DE LOTE
                |--------------------------------------------------------------------------
                */

                $lotNumber =
                    'INICIAL-' .
                    now()->format('YmdHisv') .
                    '-' .
                    $i;

                /*
                |--------------------------------------------------------------------------
                | MATERIA PRIMA
                |--------------------------------------------------------------------------
                */

                if ($type === 'raw_material') {

                    $rawMaterialId = $request->input(
                        "item{$i}_raw_material_id"
                    );

                    if (!$rawMaterialId) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Buscar materia prima
                    |--------------------------------------------------------------------------
                    */

                    $rawMaterial = RawMaterial::findOrFail(
                        $rawMaterialId
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | ACTUALIZAR COSTO DE LA MATERIA PRIMA
                    |--------------------------------------------------------------------------
                    */

                    $rawMaterial->cost = $unitCost;
                    $rawMaterial->save();

                    /*
                    |--------------------------------------------------------------------------
                    | CREAR LOTE DE MATERIA PRIMA
                    |--------------------------------------------------------------------------
                    */

                    $lot = new RawMaterialLot();

                    $lot->raw_material_id = $rawMaterialId;
                    $lot->warehouse_id = $warehouseId;

                    /*
                    | El inventario inicial no proviene de una compra.
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
                    | raw_material_lots.cost almacena
                    | el COSTO TOTAL del lote.
                    */

                    $lot->cost = $totalCost;

                    $lot->status = 'Disponible';

                    $lot->save();

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUCTO TERMINADO
                |--------------------------------------------------------------------------
                */

                if ($type === 'product') {

                    /*
                    |--------------------------------------------------------------------------
                    | MANUFACTURED PRODUCT
                    |--------------------------------------------------------------------------
                    |
                    | El ID viene del request.
                    |
                    */

                    $manufacturedProductId = $request->input(
                        "item{$i}_product_id"
                    );

                    if (!$manufacturedProductId) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | COSTOS Y PRECIO VIENEN DEL REQUEST
                    |--------------------------------------------------------------------------
                    */

                    $manufacturingCost = (float) $request->input(
                        "item{$i}_manufacturing_cost"
                    );

                    $publicPrice = (float) $request->input(
                        "item{$i}_public_price"
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | UTILITY
                    |--------------------------------------------------------------------------
                    */

                    $utility = $publicPrice > 0
                        ? round((($publicPrice - $manufacturingCost) / $publicPrice) * 100, 1)
                        : 0;

                    /*
                    |--------------------------------------------------------------------------
                    | CREAR PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    $product = new Product();
                    $product->manufactured_product_id = $manufacturedProductId;
                    $product->vinil_cost = $manufacturingCost;
                    $product->costo_total = $manufacturingCost;
                    $product->subtotal = $manufacturingCost;
                    $product->costo_venta = $publicPrice;
                    $product->utility = $utility;
                    $product->save();

                    /*
                    |--------------------------------------------------------------------------
                    | CREAR LOTE DE PRODUCTO TERMINADO
                    |--------------------------------------------------------------------------
                    */

                    $lot = new ProductLot();

                    /*
                    | Aquí usamos el ID del Product recién creado.
                    */

                    $lot->product_id = $product->id;

                    /*
                    | El inventario inicial no pertenece
                    | a una orden de producción.
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
                    |--------------------------------------------------------------------------
                    | COSTO DEL PRODUCTO
                    |--------------------------------------------------------------------------
                    |
                    | Para producto terminado el costo real es
                    | manufacturing_cost, no public_price.
                    |
                    */

                    $lot->cost_per_unit = $manufacturingCost;

                    $lot->total_cost = $quantity * $manufacturingCost;

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