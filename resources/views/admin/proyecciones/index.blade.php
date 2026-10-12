@extends('layout.dashboard-master')

@section('meta.title', 'Planeación y proyección')
@section('meta.tab_title', 'Planeación y proyección | Panel de administración | ' . config('app.name'))
@section('description', 'Proyección de ventas, producción, materias primas y costos estimados.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Planeación y proyección
        </h1>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')

        {{-- Parámetros de simulación --}}
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Parámetros de simulación
            </h3>

            <form method="GET" action="{{ route('proyecciones.index') }}">
                <div class="md:row mb-4">
                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="period">Periodo de análisis</label>

                            <select name="period" id="period" class="form-field">
                                <option value="daily" {{ ($period ?? 'custom') === 'daily' ? 'selected' : '' }}>
                                    Diario
                                </option>
                                <option value="weekly" {{ ($period ?? 'custom') === 'weekly' ? 'selected' : '' }}>
                                    Semanal
                                </option>
                                <option value="monthly" {{ ($period ?? 'custom') === 'monthly' ? 'selected' : '' }}>
                                    Mensual
                                </option>
                                <option value="custom" {{ ($period ?? 'custom') === 'custom' ? 'selected' : '' }}>
                                    Personalizado
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="adjustment">Incremento o ajuste (%)</label>

                            <input
                                type="number"
                                name="adjustment"
                                id="adjustment"
                                class="form-field"
                                value="{{ $adjustment ?? 15 }}"
                                min="-100"
                                max="500"
                                step="0.01"
                                required
                            >

                            <small class="description color-gray-darken-1">
                                Aplica un incremento o reducción a las ventas
                                registradas dentro del periodo seleccionado.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="md:row mb-4" id="custom-dates">
                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="start_date">Fecha inicial</label>

                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                class="form-field"
                                value="{{ $startDate ?? request('start_date', '') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="end_date">Fecha final</label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                class="form-field"
                                value="{{ $endDate ?? request('end_date', '') }}"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn--blue--dashboard btn--wide">
                        Calcular proyección
                    </button>
                </div>
            </form>
        </section>

        {{-- Periodo seleccionado --}}
        <section class="db-panel mb-8">
            <h3 class="db-panel__title">
                Periodo de análisis
            </h3>

            <p>
                <strong>Fecha inicial:</strong>
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            </p>

            <p>
                <strong>Fecha final:</strong>
                {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>

            <p class="description color-gray-darken-1">
                Las ventas consideradas corresponden únicamente a las fechas
                seleccionadas. La demanda proyectada se calcula aplicando
                el porcentaje de ajuste a esas ventas.
            </p>
        </section>

        {{-- Resumen general --}}
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Resumen de la proyección
            </h3>

            <div class="row">
                <div class="column-statistics">
                    <strong>
                        {{ number_format($summary['sales'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Unidades vendidas en el periodo
                </div>

                <div class="column-statistics">
                    <strong>
                        {{ number_format($summary['projected_sales'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Demanda proyectada
                </div>

                <div class="column-statistics">
                    <strong>
                        {{ number_format($summary['finished_stock'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Producto terminado disponible
                </div>
            </div>

            <div class="row">
                <div class="column-statistics">
                    <strong>
                        {{ number_format($summary['pending_production'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Producción pendiente
                </div>

                <div class="column-statistics">
                    <strong>
                        {{ number_format($summary['required_production'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Producción adicional requerida
                </div>

                <div class="column-statistics">
                    <strong>
                        ${{ number_format($summary['suggested_purchase_cost'] ?? 0, 2) }}
                    </strong>
                    <br>
                    Costo estimado de compra
                </div>
            </div>

            @if(($summary['materials_with_shortage'] ?? 0) > 0)
                <p class="mt-4">
                    <strong>
                        Materias primas con faltantes:
                    </strong>
                    {{ $summary['materials_with_shortage'] }}
                </p>
            @endif

            @if(($summary['materials_with_unit_mismatch'] ?? 0) > 0)
                <p class="mt-4">
                    <strong>
                        Materias primas con unidades incompatibles:
                    </strong>
                    {{ $summary['materials_with_unit_mismatch'] }}
                </p>
            @endif
        </section>

        {{-- Proyección por producto --}}
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Proyección por producto
            </h3>

            <div class="table-responsive">
                <table class="table size-caption mx-auto md:table--responsive">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Ventas del periodo</th>
                            <th>Demanda proyectada</th>
                            <th>Existencia</th>
                            <th>Producción pendiente</th>
                            <th>Producción adicional</th>
                            <th>Receta</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $product['product_name'] ?? 'Sin nombre' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ number_format($product['sales'] ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ number_format($product['projected_sales'] ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ number_format($product['finished_stock'] ?? 0, 2) }}
                                </td>

                                <td>
                                    {{ number_format($product['pending_production'] ?? 0, 2) }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($product['required_production'] ?? 0, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $product['recipe_name'] ?? 'Sin receta' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No hay productos para mostrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Planeación de materias primas --}}
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Planeación de materias primas
            </h3>

            <div class="table-responsive">
                <table class="table size-caption mx-auto md:table--responsive">
                    <thead>
                        <tr>
                            <th>Materia prima</th>
                            <th>Unidad</th>
                            <th>Requerimiento</th>
                            <th>Existencia disponible</th>
                            <th>Compra sugerida</th>
                            <th>Costo unitario</th>
                            <th>Costo estimado</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($materials as $material)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $material['name'] ?? 'Sin nombre' }}
                                    </strong>

                                    @if($material['unit_mismatch'] ?? false)
                                        <br>
                                        <small class="description color-gray-darken-1">
                                            Revisa las unidades de la receta:
                                            {{ implode(', ', $material['mismatched_units'] ?? []) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $material['unit'] ?? '—' }}
                                </td>

                                <td>
                                    {{ number_format($material['required'] ?? 0, 3) }}
                                </td>

                                <td>
                                    {{ number_format($material['stock'] ?? 0, 3) }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($material['shortage'] ?? 0, 3) }}
                                    </strong>
                                </td>

                                <td>
                                    ${{ number_format($material['unit_cost'] ?? 0, 2) }}
                                </td>

                                <td>
                                    @if(isset($material['estimated_cost']))
                                        <strong>
                                            ${{ number_format($material['estimated_cost'], 2) }}
                                        </strong>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No hay materias primas requeridas para esta proyección.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if(count($materials))
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">
                                    Costo total estimado de compra
                                </th>
                                <th>
                                    ${{ number_format($summary['suggested_purchase_cost'] ?? 0, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </section>

        {{-- Consideraciones --}}
        <section class="db-panel">
            <h3 class="db-panel__title">
                Consideraciones de la simulación
            </h3>

            <ul>
                <li>
                    Las ventas se consultan exclusivamente entre la fecha
                    inicial y la fecha final seleccionadas.
                </li>
                <li>
                    La demanda proyectada aplica el porcentaje de ajuste
                    a las unidades vendidas dentro de ese rango.
                </li>
                <li>
                    La producción adicional descuenta el inventario terminado
                    disponible y la producción pendiente registrada.
                </li>
                <li>
                    Las materias primas se calculan con base en las recetas
                    de los productos y la producción adicional requerida.
                </li>
                <li>
                    La compra sugerida considera el requerimiento, las
                    existencias disponibles y el stock mínimo configurado.
                </li>
                <li>
                    Si las unidades de una materia prima no son compatibles
                    con las de la receta, es necesario revisar su configuración.
                </li>
            </ul>

            <p class="mt-8">
                <strong>Importante:</strong>
                esta pantalla es únicamente una simulación. No crea órdenes
                de compra, no genera órdenes de producción y no modifica
                las existencias del inventario.
            </p>
        </section>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const period = document.getElementById('period');
        const dates = document.getElementById('custom-dates');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        function updatePeriodFields() {
            const custom = period.value === 'custom';

            dates.style.display = custom ? '' : 'none';

            /*
             * Las fechas solo son obligatorias para el periodo personalizado.
             * En otros periodos, el controlador calcula las fechas del periodo.
             */
            startDate.disabled = !custom;
            endDate.disabled = !custom;

            startDate.required = custom;
            endDate.required = custom;
        }

        period.addEventListener('change', updatePeriodFields);
        updatePeriodFields();
    });
</script>
@endsection
