<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\ProductRecipe;
use App\Models\ProductionOrderItem;
use App\Models\ProductionOrderProduct;
use App\Models\RawMaterialMovement;
use App\Models\RawMaterialLot;
use App\Models\ProductLot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductionOrderRequest;
use DB; 

class ProductionOrderController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.productionorders') || Gate::allows('create.productionorders'), 403);

        $actual_month = Carbon::now()->month;
        $actual_year  = Carbon::now()->year;

        $search = request('search');
        $month  = request('month') ?? $actual_month;
        $year   = request('year') ?? $actual_year;

        $years = collect([]);

        for ($año = 2025; $año <= $actual_year; $año++) {
            $years[$año] = $año;
        }

        $months = collect([
            '1'  => 'Enero',
            '2'  => 'Febrero',
            '3'  => 'Marzo',
            '4'  => 'Abril',
            '5'  => 'Mayo',
            '6'  => 'Junio',
            '7'  => 'Julio',
            '8'  => 'Agosto',
            '9'  => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ]);

        $statusLabels = [
            'creada'      => 'Borradores',
            'autorizada'  => 'Autorizadas',
            'produccion'  => 'En producción',
            'finalizada'  => 'Finalizadas',
            'cancelada'   => 'Canceladas',
        ];

        $ordersByStatus = collect([]);

        foreach ($statusLabels as $status => $label) {

            $query = ProductionOrder::with(['products.product','authorizer'])
            ->where('status', $status)
            ->whereMonth('issue_date', $month)
            ->whereYear('issue_date', $year)
            ->orderBy('issue_date', 'DESC');

            if ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('products.product',
                        function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', "%{$search}%"
                            );
                        }
                    );
                });
            }

            $paginatedOrders = $query->paginate(10)->appends(request()->all());

            $items = collect($paginatedOrders->items())->map(function ($order) {
                $order->products_list = $order->products->map(function ($item) {
                            return $item->product->name . ' (' . number_format($item->quantity, 3) . ')';
                        })->implode(', ');

                $order->total_quantity = $order->products->sum('quantity');
                return $order;
            });

            $ordersByStatus[$status] = ['items' => $items, 'links' => $paginatedOrders->links('layout.pagination')];
        }

        return view('admin.produccion.index',
            compact(
                'years',
                'months',
                'actual_month',
                'actual_year',
                'ordersByStatus',
                'statusLabels'
            )
        );
    }

    public function details($id)
    {
        abort_unless(
            Gate::allows('view.productionorders')
            || Gate::allows('create.productionorders'),
            403
        );

        $order = ProductionOrder::with([

            // Productos de la orden
            'products.product',
            'products.recipe',
            'products.recipe.items.rawMaterial',

            // Materias primas consolidadas
            'items.rawMaterial',

            // Usuario autorizador
            'authorizer'

        ])->findOrFail($id);

        return view(
            'admin.produccion.detalles',
            compact('order')
        );
    }
    public function create()
    {
        abort_unless(Gate::allows('create.productionorders'), 403);

        $recipes = ProductRecipe::with('product')
            ->where('active', 1)
            ->get()
            ->pluck('product.name', 'id');

        $statusLabels = collect([
            'Creada'       => 'Creada',
            'Autorizada'  => 'Autorizada',
            'Producción' => 'Producción',
            'Finalizada'   => 'Finalizada',
            'Cancelada'   => 'Cancelada',
        ]);

        return view(
            'admin.produccion.crear',
            compact(
                'recipes',
                'statusLabels'
            )
        );
    }


    public function save(ProductionOrderRequest $request)
    {
        abort_unless(Gate::allows('view.productionorders') || Gate::allows('create.productionorders'), 403);

        DB::beginTransaction();

        try {

            $productionOrder = new ProductionOrder;
            $productionOrder->order_number = $this->generateOrderNumber();
            $productionOrder->issue_date = $request->issue_date;
            $productionOrder->delivery_date = $request->delivery_date;
            $productionOrder->status = $request->status;
            $productionOrder->notes = $request->notes;
            $productionOrder->estimated_cost = 0;
            $productionOrder->active = 1;
            $productionOrder->save();

            $rawMaterials = [];
            $totalCost = 0;

            for ($i = 1; $i <= $request->item_count; $i++) {

                $recipeId = $request->input('item' . $i . '_recipes_id');
                $quantity = (float) $request->input( 'item' . $i . '_quantity');

                if (!$recipeId || !$quantity) {
                    continue;
                }

                $recipe = ProductRecipe::with(['product', 'items.rawMaterial'])->findOrFail($recipeId);

                $productionProduct = new ProductionOrderProduct;
                $productionProduct->production_order_id = $productionOrder->id;
                $productionProduct->product_id = $recipe->product_id;
                $productionProduct->product_recipe_id = $recipe->id;
                $productionProduct->quantity = $quantity;
                $productionProduct->estimated_cost = 0;
                $productionProduct->save();


                $factor = $quantity / $recipe->yield_quantity;

                $productCost = 0;

                foreach ($recipe->items as $recipeItem) {

                    $requiredQuantity = $recipeItem->quantity * $factor;
                    $cost = $requiredQuantity * $recipeItem->rawMaterial->cost;
                    $productCost += $cost;

                    if (!isset($rawMaterials[$recipeItem->raw_material_id])) {

                        $rawMaterials[$recipeItem->raw_material_id] = [
                            'raw_material_id' => $recipeItem->raw_material_id,
                            'quantity' => 0,
                            'unit' => $recipeItem->unit,
                            'estimated_cost' => 0
                        ];
                    }

                    $rawMaterials[$recipeItem->raw_material_id]['quantity'] += $requiredQuantity;
                    $rawMaterials[$recipeItem->raw_material_id]['estimated_cost'] += $cost;
                }

                $productionProduct->estimated_cost = $productCost;
                $productionProduct->save();
                $totalCost += $productCost;
            }


            foreach ($rawMaterials as $material) {

                $item = new ProductionOrderItem;
                $item->production_order_id = $productionOrder->id;
                $item->raw_material_id = $material['raw_material_id'];
                $item->quantity = $material['quantity'];
                $item->unit = $material['unit'];
                $item->estimated_cost = $material['estimated_cost'];
                $item->save();
            }

            $productionOrder->estimated_cost = $totalCost;
            $productionOrder->save();

            DB::commit();

            alert('Se ha generado la orden de producción.');

            return response('', 204, [
                'Redirect-To' => url('admin/produccion')
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            alert($e->getMessage(), 'danger');

            return response('', 204, [
                'Redirect-To' => url('admin/produccion')
            ]);
        }
    }

    public function edit($id)
    {
        abort_unless(
            Gate::allows('view.roles') || Gate::allows('create.roles'),
            403
        );

        $order = ProductionOrder::with([
            'products.recipe.product',
            'items.rawMaterial'
        ])->findOrFail($id);

        $recipes = ProductRecipe::with('product')
        ->get()
        ->pluck('product.name', 'id');

        $statusLabels = collect([
            'Creada'       => 'Creada',
            'Autorizada'  => 'Autorizada',
            'Producción' => 'Producción',
            'Finalizada'   => 'Finalizada',
            'Cancelada'   => 'Cancelada',
        ]);
       $assignedRecipes = $order->products->map(function ($product) {
            return [
                'id'                => $product->id,
                'product_recipe_id' => $product->product_recipe_id,
                'quantity'          => $product->quantity,
                'estimated_cost'    => $product->estimated_cost,
                'product_type'      => $product->product_type,
            ];
        })->values()->toArray();

        return view(
            'admin.produccion.editar',
            compact(
                'order',
                'recipes',
                'statusLabels',
                'assignedRecipes'
            )
        );
    }

    public function update(ProductionOrderRequest $request, $id)
    {
        abort_unless(
            Gate::allows('view.productionorders') ||
            Gate::allows('create.productionorders'),
            403
        );

        DB::beginTransaction();

        try {

            $productionOrder = ProductionOrder::with([
                'products.recipe.product',
                'items.rawMaterial'
            ])->findOrFail($id);

            $oldStatus = $productionOrder->status;

            $productionOrder->issue_date = $request->issue_date;
            $productionOrder->delivery_date = $request->delivery_date;
            $productionOrder->status = $request->status;
            $productionOrder->notes = $request->notes;

            if (
                $oldStatus != 'Autorizada' &&
                $request->status == 'Autorizada'
            ) {
                $productionOrder->authorized_by = auth()->id();
            }

            $productionOrder->estimated_cost = 0;
            $productionOrder->save();

            /*
            |--------------------------------------------------------------------------
            | Limpiar detalle anterior
            |--------------------------------------------------------------------------
            */

            $productionOrder->products()->delete();
            $productionOrder->items()->delete();

            /*
            |--------------------------------------------------------------------------
            | Reconstruir productos y materias primas
            |--------------------------------------------------------------------------
            */

            $rawMaterials = [];
            $totalCost = 0;

            for ($i = 1; $i <= $request->item_count; $i++) {

                $recipeId = $request->input(
                    'item' . $i . '_recipes_id'
                );

                $quantity = (float) $request->input(
                    'item' . $i . '_quantity'
                );

                if (!$recipeId || !$quantity) {
                    continue;
                }

                $recipe = ProductRecipe::with([
                    'product',
                    'items.rawMaterial'
                ])->findOrFail($recipeId);

                $productionProduct = new ProductionOrderProduct;
                $productionProduct->production_order_id =
                    $productionOrder->id;

                $productionProduct->product_id =
                    $recipe->product_id;

                $productionProduct->product_recipe_id =
                    $recipe->id;

                $productionProduct->quantity =
                    $quantity;

                $productionProduct->estimated_cost = 0;

                $productionProduct->save();

                $factor =
                    $quantity /
                    $recipe->yield_quantity;

                $productCost = 0;

                foreach ($recipe->items as $recipeItem) {

                    $requiredQuantity =
                        $recipeItem->quantity * $factor;

                    $cost =
                        $requiredQuantity *
                        $recipeItem->rawMaterial->cost;

                    $productCost += $cost;

                    if (
                        !isset(
                            $rawMaterials[
                                $recipeItem->raw_material_id
                            ]
                        )
                    ) {

                        $rawMaterials[
                            $recipeItem->raw_material_id
                        ] = [

                            'raw_material_id' =>
                                $recipeItem->raw_material_id,

                            'quantity' => 0,

                            'unit' =>
                                $recipeItem->unit,

                            'estimated_cost' => 0
                        ];
                    }

                    $rawMaterials[
                        $recipeItem->raw_material_id
                    ]['quantity'] += $requiredQuantity;

                    $rawMaterials[
                        $recipeItem->raw_material_id
                    ]['estimated_cost'] += $cost;
                }

                $productionProduct->estimated_cost =
                    $productCost;

                $productionProduct->save();

                $totalCost += $productCost;
            }

            foreach ($rawMaterials as $material) {

                $item = new ProductionOrderItem;
                $item->production_order_id =
                    $productionOrder->id;

                $item->raw_material_id =
                    $material['raw_material_id'];

                $item->quantity =
                    $material['quantity'];

                $item->unit =
                    $material['unit'];

                $item->estimated_cost =
                    $material['estimated_cost'];

                $item->save();
            }

            $productionOrder->estimated_cost =
                $totalCost;

            $productionOrder->save();

            /*
            |--------------------------------------------------------------------------
            | Cambio a Producción
            |--------------------------------------------------------------------------
            */

            if (
                $oldStatus != 'Producción' &&
                $request->status == 'Producción'
            ) {

                $productionOrder->load('items.rawMaterial');

                $stockErrors = [];

                foreach ($productionOrder->items as $item) {

                    $required = $item->quantity;

                    $availableStock = RawMaterialLot::where(
                        'raw_material_id',
                        $item->raw_material_id
                    )
                    ->where('available_quantity', '>', 0)
                    ->sum('available_quantity');

                    if ($availableStock < $required) {

                        $stockErrors[] =
                            $item->rawMaterial->name .
                            ' (Faltan: ' .
                            number_format(
                                $required - $availableStock,
                                3
                            ) .
                            ' ' .
                            $item->unit .
                            ', Disponible: ' .
                            number_format(
                                $availableStock,
                                3
                            ) .
                            ' ' .
                            $item->unit .
                            ')';
                    }
                }

                if (!empty($stockErrors)) {

                    throw new \Exception(
                        'No es posible iniciar la producción.<br>' .
                        implode('<br>', $stockErrors)
                    );
                }

                foreach ($productionOrder->items as $item) {

                    $remaining = $item->quantity;

                    $lots = RawMaterialLot::where(
                        'raw_material_id',
                        $item->raw_material_id
                    )
                    ->where('available_quantity', '>', 0)
                    ->orderBy('entry_date')
                    ->orderBy('id')
                    ->get();

                    foreach ($lots as $lot) {

                        if ($remaining <= 0) {
                            break;
                        }

                        $consume = min(
                            $remaining,
                            $lot->available_quantity
                        );

                        $lot->available_quantity -= $consume;

                        if ($lot->available_quantity <= 0) {

                            $lot->available_quantity = 0;
                            $lot->status = 'Consumido';
                        }

                        $lot->save();

                        RawMaterialMovement::create([
                            'raw_material_id'     => $item->raw_material_id,
                            'raw_material_lot_id' => $lot->id,
                            'movement_type'       => 'consumo_produccion',
                            'quantity'            => -$consume,
                            'reference_type'      => 'production_order',
                            'reference_id'        => $productionOrder->id,
                            'created_by'          => auth()->id(),
                        ]);

                        $remaining -= $consume;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cambio a Finalizada
            |--------------------------------------------------------------------------
            */

            if (
                $oldStatus != 'Finalizada' &&
                $request->status == 'Finalizada'
            ) {

                $productionOrder->load(
                    'products.recipe.product'
                );

                foreach (
                    $productionOrder->products
                    as $productionProduct
                ) {

                    $product =
                        $productionProduct->recipe->product;

                    $lotNumber =
                        strtoupper(
                            substr(
                                $product->name,
                                0,
                                3
                            )
                        )
                        . '-'
                        . now()->format('Ymd')
                        . '-'
                        . str_pad(
                            $productionOrder->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        )
                        . '-'
                        . $productionProduct->id;

                    ProductLot::create([

                        'product_id' =>
                            $product->id,

                        'production_order_id' =>
                            $productionOrder->id,

                        'warehouse_id' => 4,

                        'lot_number' =>
                            $lotNumber,

                        'production_date' =>
                            now(),

                        'expiration_date' =>
                            now()->addDays(
                                $product->expiration_days ?? 30
                            ),

                        'initial_quantity' =>
                            $productionProduct->quantity,

                        'available_quantity' =>
                            $productionProduct->quantity,

                        'cost_per_unit' =>
                            (
                                $productionProduct->estimated_cost
                                /
                                max(
                                    $productionProduct->quantity,
                                    1
                                )
                            ),

                        'total_cost' =>
                            $productionProduct->estimated_cost,

                        'status' => 'Disponible',

                        'active' => 1,
                    ]);
                }
            }

            DB::commit();

            alert('Se ha actualizado la orden de producción.');

            return response('', 204, [
                'Redirect-To' => url('admin/produccion')
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            alert($e->getMessage(), 'danger');

            return response('', 204, [
                'Redirect-To' => url('admin/produccion')
            ]);
        }
    }


    public function delete($id)
    {
        abort_unless(
            Gate::allows('delete.productionorders'),
            403
        );

        $order = ProductionOrder::findOrFail($id);

        $order->active = 0;
        $order->save();

        alert('La orden de producción ha sido eliminada.');

        return response('', 204);
    }

    private function generateOrderNumber()
    {
        $lastOrder = ProductionOrder::latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        return 'OP-' . now()->format('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }
}
