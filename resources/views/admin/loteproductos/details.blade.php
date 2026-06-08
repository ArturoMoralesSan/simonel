@extends('layout.dashboard-master')

@section('title', 'Detalle lote')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Lote {{ $lot->lot_number }}
    </h1>
</div>

<div class="fluid-container">

    <section class="db-panel">

        <div class="row">

            <div class="md:col-1/2">
                <strong>Producto</strong>
                <p>{{ $lot->product->name }}</p>
            </div>

            <div class="md:col-1/2">
                <strong>Orden Producción</strong>
                <p>
                    {{ optional($lot->order)->order_number }}
                </p>
            </div>

        </div>

        <hr>

        <div class="row">

            <div class="md:col-1/3">
                <strong>Producción</strong>
                <p>
                    {{ $lot->production_date->format('d/m/Y') }}
                </p>
            </div>

            <div class="md:col-1/3">
                <strong>Caducidad</strong>
                <p>
                    {{ $lot->expiration_date->format('d/m/Y') }}
                </p>
            </div>

            <div class="md:col-1/3">
                <strong>Estado</strong>
                <p>{{ $lot->status }}</p>
            </div>

        </div>

        <hr>

        <div class="row">

            <div class="md:col-1/3">
                <strong>Cantidad inicial</strong>
                <p>{{ $lot->initial_quantity }}</p>
            </div>

            <div class="md:col-1/3">
                <strong>Disponible</strong>
                <p>{{ $lot->available_quantity }}</p>
            </div>

            <div class="md:col-1/3">
                <strong>Costo unitario</strong>
                <p>
                    ${{ number_format($lot->cost_per_unit,2) }}
                </p>
            </div>

        </div>

        <hr>

        <a
            href="{{ url('admin/lotes-producto/'.$lot->id.'/etiqueta') }}"
            target="_blank"
            class="btn btn--primary"
        >
            Imprimir etiqueta
        </a>

    </section>

</div>

@endsection