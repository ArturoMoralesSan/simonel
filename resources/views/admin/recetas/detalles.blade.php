@extends('layout.dashboard-master')

@section('title', 'Detalle de receta')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Detalle de receta
    </h1>
</div>

<div class="fluid-container">
    <section class="db-panel">
        <h3 class="db-panel__title">
            Información general
        </h3>
        <div class="row">
            <div class="md:col-1/2">
                <strong>Producto</strong>
                <p>{{ $recipe->manufactured->name }}</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="md:col-1/2">
                <strong>Rendimiento</strong>
                <p>
                    {{ $recipe->yield_quantity }}
                    {{ $recipe->unit }}
                </p>
            </div>
            <div class="md:col-1/2">
                <strong>Notas</strong>
                <p>{{ $recipe->notes ?? '-' }}</p>
            </div>
        </div>

    </section>
    <section class="db-panel mt-8">
        <h3 class="db-panel__title">
            Materias primas
        </h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Materia prima</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Merma %</th>
                    <th>Costo Unitario</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recipe->items as $item)
                    <tr>
                        <td>
                            {{ $item->rawMaterial->name }}
                        </td>
                        <td>
                            {{ $item->quantity }}
                        </td>
                        <td>
                            {{ $item->unit }}
                        </td>
                        <td>
                            {{ $item->waste_percent }} %
                        </td>
                        <td>
                            ${{ number_format($item->rawMaterial->cost,2) }}
                        </td>
                        <td>
                            ${{ number_format($item->calculated_cost,2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="db-panel mt-8">
        <h3 class="db-panel__title">
            Resumen de costos
        </h3>
        <div class="row">
            <div class="md:col-1/2">
                <strong>Costo total insumos</strong>
                <p>
                    ${{ number_format($totalCost,2) }}
                </p>
            </div>
            <div class="md:col-1/2">
                <strong>Costo por kg producido</strong>
                <p>
                    ${{ number_format($costPerKg,2) }}
                </p>
            </div>
        </div>
    </section>

</div>

@endsection