<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\RawMaterialLot;
use App\Models\PurchaseBoning;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;


class PurchaseController extends Controller
{

    public function index()
    {
        abort_unless(
            Gate::allows('view.purchase') || Gate::allows('create.purchase'),
            403
        );

        $search = request('search');

        // Fechas por defecto: último mes hasta hoy
        $dateNow = Carbon::now();

        $start_date = request('start_date')
            ?? $dateNow->copy()->subMonth()->format('Y-m-d');

        $end_date = request('end_date')
            ?? $dateNow->format('Y-m-d');

        $purchases = Purchase::with([
            'supplier',
            'items.rawMaterial',
            'lots:id,purchase_id,lot_number'
        ])
            ->withCount([
                'items',
                'lots'
            ])
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->whereHas('supplier', function ($supplier) use ($search) {

                        $supplier
                            ->where(
                                'business_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'trade_name',
                                'like',
                                "%{$search}%"
                            );

                    })
                    ->orWhere(
                        'invoice_number',
                        'like',
                        "%{$search}%"
                    );
                });
            })
            ->whereBetween('purchase_date', [
                $start_date,
                $end_date
            ])
            ->orderByDesc('id')
            ->paginate(20);

        $purchaseItems = collect($purchases->items())
            ->map(function ($purchase) {

                $purchase->purchase_date_formatted = Carbon::parse(
                    $purchase->purchase_date
                )->format('d/m/Y');

                // Todos los lot_number de esta compra separados por coma
                $purchase->lot_numbers = $purchase->lots
                    ->pluck('lot_number')
                    ->filter()
                    ->implode(', ');

                return $purchase;
            });

        $links = $purchases
            ->appends(request()->query())
            ->links('layout.pagination');

        return view(
            'admin.compras.index',
            compact(
                'purchaseItems',
                'links',
                'start_date',
                'end_date'
            )
        );
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('business_name')->pluck('business_name', 'id');

        $rawmaterials = RawMaterial::orderBy('name')->pluck('name', 'id');

        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');

        return view('admin.compras.crear', compact('warehouses', 'suppliers','rawmaterials'));
    }

public function details($id)
{
    abort_unless(Gate::allows('view.purchase'), 403);

    $purchase = Purchase::with([
        'supplier',
        'items.rawMaterial',
        'lots.warehouse',
        'boning'
    ])->findOrFail($id);

    return view(
        'admin.compras.details',
        compact('purchase')
    );
}

public function save(PurchaseRequest $request)
{
    DB::transaction(function () use ($request) {

        /*
        |--------------------------------------------------------------------------
        | PURCHASE
        |--------------------------------------------------------------------------
        */

        if ($request->purchase_id) {

            $purchase = Purchase::findOrFail(
                $request->purchase_id
            );

            $purchase->update([
                'supplier_id'    => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'purchase_date'  => $request->purchase_date,
                'notes'          => $request->notes,
            ]);

        } else {

            $purchase = Purchase::create([
                'supplier_id'    => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'purchase_date'  => $request->purchase_date,
                'subtotal'       => 0,
                'total'          => 0,
                'notes'          => $request->notes,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | PURCHASE ITEMS
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        for (
            $i = 1;
            $i <= $request->item_count;
            $i++
        ) {

            $rawMaterialId = $request->input(
                "item{$i}_raw_material_id"
            );

            if (!$rawMaterialId) {
                continue;
            }

            $quantity = (float) $request->input(
                "item{$i}_quantity"
            );

            $unitCost = (float) $request->input(
                "item{$i}_unit_cost"
            );

            $totalCost = $quantity * $unitCost;


            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR COSTO DE MATERIA PRIMA
            |--------------------------------------------------------------------------
            */

            $rawMaterial = RawMaterial::find(
                $rawMaterialId
            );

            if ($rawMaterial) {

                $rawMaterial->cost = $unitCost;
                $rawMaterial->save();
            }


            /*
            |--------------------------------------------------------------------------
            | PURCHASE ITEM
            |--------------------------------------------------------------------------
            */

            $purchaseItem = PurchaseItem::firstOrNew([
                'purchase_id'     => $purchase->id,
                'raw_material_id' => $rawMaterialId,
            ]);

            $purchaseItem->quantity = $quantity;
            $purchaseItem->unit_cost = $unitCost;
            $purchaseItem->total_cost = $totalCost;

            $purchaseItem->save();


            /*
            |--------------------------------------------------------------------------
            | LOT
            |--------------------------------------------------------------------------
            */

            $lot = RawMaterialLot::where(
                'purchase_id',
                $purchase->id
            )
                ->where(
                    'raw_material_id',
                    $rawMaterialId
                )
                ->first();

            if ($lot) {

                $lot->update([
                    'warehouse_id' => $request->input(
                        "item{$i}_warehouse_id"
                    ),

                    'supplier_id' => $request->supplier_id,

                    'supplier_lot' => $request->input(
                        "item{$i}_supplier_lot"
                    ),

                    'entry_date' => $purchase->purchase_date,

                    'expiration_date' => $request->input(
                        "item{$i}_expiration_date"
                    ),

                    'initial_quantity' => $quantity,

                    'available_quantity' => $quantity,

                    'cost' => $totalCost,
                ]);

            } else {

                RawMaterialLot::create([
                    'raw_material_id' => $rawMaterialId,

                    'warehouse_id' => $request->input(
                        "item{$i}_warehouse_id"
                    ),

                    'purchase_id' => $purchase->id,

                    'supplier_id' => $request->supplier_id,

                    'lot_number' =>
                        'COMP-' .
                        $purchase->id .
                        '-' .
                        $i,

                    'supplier_lot' => $request->input(
                        "item{$i}_supplier_lot"
                    ),

                    'entry_date' => $purchase->purchase_date,

                    'expiration_date' => $request->input(
                        "item{$i}_expiration_date"
                    ),

                    'initial_quantity' => $quantity,

                    'available_quantity' => $quantity,

                    'cost' => $totalCost,

                    'status' => 'Disponible',
                ]);
            }

            $subtotal += $totalCost;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR TOTALES DE LA COMPRA
        |--------------------------------------------------------------------------
        */

        $purchase->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);


        /*
        |--------------------------------------------------------------------------
        | PURCHASE BONING
        |--------------------------------------------------------------------------
        |
        | El deshuese pertenece a la compra mediante purchase_id.
        | Como purchase_id es UNIQUE en purchase_boning, usamos updateOrCreate.
        |
        */

        PurchaseBoning::updateOrCreate(

            [
                'purchase_id' => $purchase->id,
            ],

            [
                'pierna' => $request->input(
                    'deshuese_pulpa'
                ),

                'pulpa' => $request->input(
                    'deshuese_pulpa'
                ),

                'hueso' => $request->input(
                    'deshuese_hueso'
                ),

                'lonja' => $request->input(
                    'deshuese_lonja'
                ),

                'cuero_planchar' => $request->input(
                    'deshuese_cuero_planchado'
                ),

                'chamorro' => $request->input(
                    'deshuese_chamorro'
                ),

                'costilla' => $request->input(
                    'deshuese_costilla'
                ),

                'piernas' => $request->input(
                    'deshuese_piernas'
                ),

                'paleta' => $request->input(
                    'deshuese_paleta'
                ),

                'lomo' => $request->input(
                    'deshuese_lomo'
                ),

                'espinazo' => $request->input(
                    'deshuese_espinazo'
                ),
            ]
        );
    });


    alert('La compra se ha guardado correctamente.');

    return response('', 204, [
        'Redirect-To' => url('admin/compras')
    ]);
}



    public function edit($id)
    {
        $purchase = Purchase::with([
            'items',
            'lots',
            'boning'
        ])->findOrFail($id);
        $purchase->items->transform(function ($item) use ($purchase) {

            $lot = $purchase->lots
                ->where('raw_material_id', $item->raw_material_id)
                ->first();

            $item->warehouse_id = $lot->warehouse_id ?? null;
            $item->supplier_lot = $lot->supplier_lot ?? null;
            
            $item->expiration_date_input = optional($lot->expiration_date)
            ? \Carbon\Carbon::parse($lot->expiration_date)->format('Y-m-d')
            : null;

            return $item;
        });
        $suppliers = Supplier::orderBy('business_name')->pluck('business_name', 'id');
        $rawmaterials = RawMaterial::orderBy('name')->pluck('name', 'id');
        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');

        return view('admin.compras.editar', compact(
            'purchase',
            'suppliers',
            'rawmaterials',
            'warehouses'
        ));
    }

    public function update(PurchaseRequest $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $purchase->update([
            'supplier_id'    => $request->supplier_id,
            'invoice_number' => $request->invoice_number,
            'purchase_date'  => $request->purchase_date,
            'notes'          => $request->notes,
        ]);

        return redirect('admin/compras')
            ->with('success', 'Compra actualizada correctamente');
    }

    public function delete($id)
    {
        $purchase = Purchase::findOrFail($id);

        $purchase->delete();

        alert('No se ha podido eliminar un usuario.');
        return response('', 204);

    }
}