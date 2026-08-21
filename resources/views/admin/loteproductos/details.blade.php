@extends('layout.dashboard-master')

@section('title', 'Detalle lote')
@section('css_classes', 'dashboard')
@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Lote {{ $lot->lot_number }}
    </h1>
</div>

<div class="fluid-container">
    <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/lotes-producto/') }}">Ver todos los lotres de producto terminado</a>
        </p>
    <section class="db-panel">

        <div class="row">

            <div class="md:col-1/2">
                <strong>Producto</strong>
                <p>{{ $lot->product->manufactured->name }}</p>
            </div>

           <div class="md:col-1/2">
                <strong>Orden Producción</strong>
                <p>
                    {{ optional($lot->order)->order_number ?? 'Sin orden de producción' }}
                </p>
            </div>

        </div>

        <hr>

        <div class="row">

            <div class="md:col-1/3">
                <strong>Producción</strong>
                <p>
                    {{ $lot->production_date ? $lot->production_date->format('d/m/Y') : 'Sin fecha' }}
                </p>
            </div>

            <div class="md:col-1/3">
                <strong>Caducidad</strong>
                <p>
                    {{ $lot->expiration_date ? $lot->expiration_date->format('d/m/Y') : 'Sin fecha de caducidad' }}
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