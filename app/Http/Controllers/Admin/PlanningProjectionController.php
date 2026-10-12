<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManufacturedProduct;
use App\Models\ProductLot;
use App\Models\RawMaterial;
use App\Models\RawMaterialLot;
use App\Models\SaleProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PlanningProjectionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            Gate::allows('view.productionorders')
                || Gate::allows('create.productionorders')
                || Gate::allows('view.inventories')
                || Gate::allows('create.inventories'),
            403
        );

        $validated = $request->validate([
            'period' => 'nullable|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'adjustment' => 'nullable|numeric|min:-100|max:500',
        ]);

        $period = $validated['period'] ?? 'custom';
        $adjustment = (float) ($validated['adjustment'] ?? 0);

        /*
         * Periodo de proyección.
         */
        if ($period === 'custom') {
            $start = Carbon::parse(
                $validated['start_date'] ?? now()->toDateString()
            )->startOfDay();

            $end = Carbon::parse(
                $validated['end_date'] ?? $start->toDateString()
            )->startOfDay();
        } else {
            $date = isset($validated['start_date'])
                ? Carbon::parse($validated['start_date'])
                : now();

            switch ($period) {
                case 'daily':
                    $start = $date->copy()->startOfDay();
                    $end = $date->copy()->startOfDay();
                    break;

                case 'weekly':
                    $start = $date->copy()->startOfWeek();
                    $end = $date->copy()->endOfWeek()->startOfDay();
                    break;

                case 'monthly':
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth()->startOfDay();
                    break;

                case 'yearly':
                    $start = $date->copy()->startOfYear();
                    $end = $date->copy()->endOfYear()->startOfDay();
                    break;

                default:
                    $start = now()->startOfDay();
                    $end = $start->copy();
                    break;
            }
        }

        if ($start->gt($end)) {
            throw ValidationException::withMessages([
                'end_date' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            ]);
        }

        $start = $start->startOfDay();
        $end = $end->startOfDay();

        $endExclusive = $end->copy()
            ->addDay()
            ->startOfDay()
            ->toDateTimeString();

        $today = now()->toDateString();

        /*
         * Normalización y conversión de unidades.
         */
        $normalizeUnit = function ($unit) {
            $unit = mb_strtolower(trim((string) $unit));

            $aliases = [
                'kilogramo' => 'kg',
                'kilogramos' => 'kg',
                'kilo' => 'kg',
                'kilos' => 'kg',
                'gramo' => 'g',
                'gramos' => 'g',
                'miligramo' => 'mg',
                'miligramos' => 'mg',
                'litro' => 'l',
                'litros' => 'l',
                'lt' => 'l',
                'lts' => 'l',
                'mililitro' => 'ml',
                'mililitros' => 'ml',
                'pieza' => 'pza',
                'piezas' => 'pza',
                'unidad' => 'pza',
                'unidades' => 'pza',
            ];

            return $aliases[$unit] ?? $unit;
        };

        $convertQuantity = function (
            $quantity,
            $fromUnit,
            $toUnit
        ) use ($normalizeUnit) {
            $from = $normalizeUnit($fromUnit);
            $to = $normalizeUnit($toUnit);

            if ($from === $to) {
                return (float) $quantity;
            }

            $mass = [
                'kg' => 1000,
                'g' => 1,
                'mg' => 0.001,
            ];

            $volume = [
                'l' => 1000,
                'ml' => 1,
            ];

            if (isset($mass[$from], $mass[$to])) {
                return (float) $quantity
                    * $mass[$from]
                    / $mass[$to];
            }

            if (isset($volume[$from], $volume[$to])) {
                return (float) $quantity
                    * $volume[$from]
                    / $volume[$to];
            }

            return null;
        };

        /*
         * Productos fabricables, producto comercial y recetas activas.
         */
        $manufacturedProducts = ManufacturedProduct::query()
            ->with([
                'product',
                'recipes' => function ($query) {
                    $query->where('active', 1)
                        ->orderByDesc('id');
                },
                'recipes.items.rawMaterial',
            ])
            ->orderBy('name')
            ->get();

        /*
         * Ventas históricas del periodo seleccionado.
         * Se conserva la consulta original y sus estados.
         */
        $sales = SaleProduct::query()
            ->join('sales as s', 's.id', '=', 'sale_products.sale_id')
            ->whereNull('s.deleted_at')
            ->whereNull('sale_products.deleted_at')
            ->whereIn('s.status', [
                'paid',
                'credit',
                'assortment',
            ])
            ->where('s.created_at', '>=', $start)
            ->where('s.created_at', '<', $endExclusive)
            ->selectRaw(
                'sale_products.product_id, SUM(sale_products.quantity) AS total_quantity'
            )
            ->groupBy('sale_products.product_id')
            ->pluck('total_quantity', 'product_id');

        /*
         * Existencia de producto terminado.
         * Se utiliza available_quantity de product_lots.
         * No se suman las cantidades de órdenes de producción.
         */
        $finishedStock = ProductLot::query()
            ->where('active', 1)
            ->where('status', 'Disponible')
            ->where('available_quantity', '>', 0)
            ->where(function ($query) use ($today) {
                $query->whereNull('expiration_date')
                    ->orWhereDate('expiration_date', '>=', $today);
            })
            ->selectRaw(
                'product_id, SUM(available_quantity) AS total_quantity'
            )
            ->groupBy('product_id')
            ->pluck('total_quantity', 'product_id');

        /*
         * Catálogo de materias primas activas.
         */
        $rawMaterials = RawMaterial::query()
            ->where('active', 1)
            ->get()
            ->keyBy('id');

        /*
         * Existencia de materias primas en sus lotes.
         */
        $rawMaterialStock = RawMaterialLot::query()
            ->where('available_quantity', '>', 0)
            ->where(function ($query) use ($today) {
                $query->whereNull('expiration_date')
                    ->orWhereDate('expiration_date', '>=', $today);
            })
            ->selectRaw(
                'raw_material_id, SUM(available_quantity) AS total_quantity'
            )
            ->groupBy('raw_material_id')
            ->pluck('total_quantity', 'raw_material_id');

        /*
         * Inicialización de resultados.
         */
        $productRows = [];
        $materialRequirements = [];

        $summary = [
            'sales' => 0,
            'projected_sales' => 0,
            'finished_stock' => 0,
            'pending_production' => 0,
            'required_production' => 0,
            'materials_required' => 0,
            'suggested_purchase_cost' => 0,
            'materials_with_shortage' => 0,
            'materials_with_unit_mismatch' => 0,
            'products_without_recipe' => 0,
        ];

        /*
         * Proyección por producto fabricable.
         */
        foreach ($manufacturedProducts as $manufactured) {
            $manufacturedId = (int) $manufactured->id;

            $commercialProduct = $manufactured->product;

            $productId = $commercialProduct
                ? (int) $commercialProduct->id
                : null;

            $salesQuantity = $productId !== null
                ? (float) ($sales[$productId] ?? 0)
                : 0;

            /*
             * Ventas proyectadas con el ajuste indicado.
             */
            $projectedQuantity = max(
                0,
                $salesQuantity * (1 + ($adjustment / 100))
            );

            /*
             * Inventario de producto terminado disponible.
             */
            $stock = $productId !== null
                ? (float) ($finishedStock[$productId] ?? 0)
                : 0;

            /*
             * Producción adicional:
             * demanda proyectada - existencia de producto terminado.
             * No se descuentan las cantidades de órdenes abiertas.
             */
            $productionRequired = max(
                0,
                $projectedQuantity - $stock
            );

            /*
             * Receta activa del producto fabricable.
             */
            $recipe = $manufactured->recipes->first();

            $recipeYield = $recipe
                ? (float) $recipe->yield_quantity
                : 0;

            $recipeLabel = $recipe
                ? 'Receta #' . $recipe->id
                : 'Sin receta';

            $ingredientCount = $recipe
                ? $recipe->items->count()
                : 0;

            /*
             * ÚNICO CAMBIO:
             * omitir productos sin receta activa, sin rendimiento
             * válido o sin ingredientes registrados.
             */
            if (!$recipe || $recipeYield <= 0 || $ingredientCount === 0) {
                continue;
            }

            /*
             * Materias primas necesarias para fabricar únicamente
             * las unidades que no cubre el inventario terminado.
             */
            if ($productionRequired > 0) {
                /*
                 * Mantiene la misma lógica de rendimiento utilizada
                 * por las órdenes de producción:
                 * cantidad de ingrediente * (producción / rendimiento).
                 */
                $factor = $productionRequired / $recipeYield;

                foreach ($recipe->items as $item) {
                    $recipeMaterial = $item->rawMaterial;

                    if (!$recipeMaterial) {
                        continue;
                    }

                    $material = $rawMaterials->get(
                        $recipeMaterial->id
                    );

                    if (!$material) {
                        continue;
                    }

                    $sourceUnit = $item->unit
                        ?: $recipeMaterial->unit;

                    $targetUnit = $material->unit;

                    $quantity = (float) $item->quantity * $factor;

                    $wastePercent = max(
                        0,
                        (float) ($item->waste_percent ?? 0)
                    );

                    $quantityWithWaste = $quantity
                        * (1 + ($wastePercent / 100));

                    $convertedQuantity = $convertQuantity(
                        $quantityWithWaste,
                        $sourceUnit,
                        $targetUnit
                    );

                    /*
                     * Crear el registro aunque las unidades sean
                     * incompatibles, para que el problema sea visible.
                     */
                    if (!isset($materialRequirements[$material->id])) {
                        $materialRequirements[$material->id] = [
                            'id' => $material->id,
                            'name' => $material->name,
                            'unit' => $normalizeUnit($material->unit),
                            'unit_cost' => (float) $material->cost,
                            'minimum_stock' => (float) (
                                $material->minimum_stock ?? 0
                            ),
                            'stock' => (float) (
                                $rawMaterialStock[$material->id] ?? 0
                            ),
                            'required' => 0,
                            'unit_mismatch' => false,
                            'mismatched_units' => [],
                        ];
                    }

                    if ($convertedQuantity === null) {
                        $materialRequirements[$material->id]['unit_mismatch']
                            = true;

                        $materialRequirements[$material->id]['mismatched_units'][] =
                            $sourceUnit . ' → ' . $targetUnit;

                        continue;
                    }

                    /*
                     * Acumular ingredientes cuando una misma materia
                     * prima aparece en distintas recetas.
                     */
                    $materialRequirements[$material->id]['required']
                        += $convertedQuantity;
                }
            }

            $productRows[] = [
                'product_id' => $productId,
                'manufactured_product_id' => $manufacturedId,
                'product_name' => $manufactured->name,
                'sales' => $salesQuantity,
                'projected_sales' => $projectedQuantity,
                'finished_stock' => $stock,

                /*
                 * Se conserva esta clave para no romper la vista
                 * existente. Las órdenes abiertas no intervienen
                 * en este cálculo.
                 */
                'pending_production' => 0,
                'required_production' => $productionRequired,
                'recipe_name' => $recipeLabel,
                'recipe_yield' => $recipeYield,
                'ingredient_count' => $ingredientCount,
            ];

            $summary['sales'] += $salesQuantity;
            $summary['projected_sales'] += $projectedQuantity;
            $summary['finished_stock'] += $stock;
            $summary['required_production'] += $productionRequired;
        }

        /*
         * Faltantes, compras sugeridas y costos.
         */
        foreach ($materialRequirements as &$material) {
            $material['shortage'] = max(
                0,
                $material['required'] - $material['stock']
            );

            $material['purchase_quantity'] = max(
                0,
                $material['required']
                    + $material['minimum_stock']
                    - $material['stock']
            );

            $material['estimated_cost'] = $material['unit_mismatch']
                ? null
                : $material['purchase_quantity']
                    * $material['unit_cost'];

            $summary['materials_required'] += $material['required'];

            if ($material['shortage'] > 0) {
                $summary['materials_with_shortage']++;
            }

            if ($material['unit_mismatch']) {
                $summary['materials_with_unit_mismatch']++;
            }

            if ($material['estimated_cost'] !== null) {
                $summary['suggested_purchase_cost'] +=
                    $material['estimated_cost'];
            }
        }

        unset($material);

        usort($materialRequirements, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return view('admin.proyecciones.index', [
            'products' => $productRows,
            'materials' => array_values($materialRequirements),
            'summary' => $summary,
            'period' => $period,
            'adjustment' => $adjustment,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
        ]);
    }
}