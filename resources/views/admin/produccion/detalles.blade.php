@extends('layout.dashboard-master')

@section('title', 'Detalle de producción')
@section('tab_title', 'Detalle de producción | ' . config('app.name'))

@section('content')

<section class="mb-16">

    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Orden de Producción #{{ $order->order_number }}
        </h1>
    </div>

    <div class="fluid-container">

        <p class="mb-8">
            <span class="color-link">«</span>
            <a href="{{ url('admin/produccion') }}">
                Volver a producción
            </a>
        </p>

        {{-- Información General --}}
        <section class="db-panel">

            <h3 class="db-panel__title">
                Información General
            </h3>

            <div class="row">

                <div class="md:col-1/3">
                    <strong>Estado</strong>
                    <p>{{ ucfirst($order->status) }}</p>
                </div>

                <div class="md:col-1/3">
                    <strong>Fecha elaboración</strong>
                    <p>{{ $order->formated_issue_date }}</p>
                </div>

                <div class="md:col-1/3">
                    <strong>Fecha entrega</strong>
                    <p>{{ $order->formated_delivery_date }}</p>
                </div>

            </div>

            <hr>

            <div class="row mt-4">

                <div class="md:col-1/2">
                    <strong>Autorizó</strong>
                    <p>
                        {{ $order->authorizer
                            ? $order->authorizer->name . ' ' . $order->authorizer->last_name
                            : 'Sin autorizar'
                        }}
                    </p>
                </div>

                <div class="md:col-1/2">
                    <strong>Costo estimado</strong>
                    <p>
                        ${{ number_format($order->estimated_cost, 2) }}
                    </p>
                </div>

            </div>

            @if($order->notes)
                <hr>

                <div class="mt-4">
                    <strong>Comentarios</strong>
                    <p>{{ $order->notes }}</p>
                </div>
            @endif

        </section>

        {{-- Resumen --}}
        <section class="db-panel mt-8">

            <h3 class="db-panel__title">
                Resumen de Producción
            </h3>

            <div class="row">

                <div class="md:col-1/3">
                    <strong>Productos</strong>
                    <p>{{ $order->products->count() }}</p>
                </div>

                <div class="md:col-1/3">
                    <strong>Total a producir</strong>
                    <p>
                        {{ number_format($order->products->sum('quantity'), 3) }}
                    </p>
                </div>

                <div class="md:col-1/3">
                    <strong>Costo total</strong>
                    <p>
                        ${{ number_format($order->estimated_cost, 2) }}
                    </p>
                </div>

            </div>

        </section>

        {{-- Productos --}}
        <section class="db-panel mt-8">

            <h3 class="db-panel__title">
                Productos a producir
            </h3>

            <table class="table">

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Tipo</th>
                        <th>Costo estimado</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($order->products as $product)

                        <tr>

                            <td>
                                {{ $product->manufactured->name ?? '-' }} <span class="description">{{ $product->manufactured->description ?? '-' }}</span>
                            </td>

                            <td>
                                {{ number_format($product->quantity, 3) }}
                            </td>

                            <td>
                                {{ ucfirst($product->product_type ?? 'producto') }}
                            </td>

                            <td>
                                ${{ number_format($product->estimated_cost, 2) }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </section>

        {{-- Materias primas --}}
        <section class="db-panel mt-8">

            <h3 class="db-panel__title">
                Materias primas requeridas
            </h3>

            <table class="table">

                <thead>
                    <tr>
                        <th>Materia Prima</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Costo</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($order->items as $item)

                        <tr>

                            <td>
                                {{ $item->rawMaterial->name }}
                            </td>

                            <td>
                                {{ number_format($item->quantity, 3) }}
                            </td>

                            <td>
                                {{ $item->unit }}
                            </td>

                            <td>
                                ${{ number_format($item->estimated_cost, 2) }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

                <tfoot>

                    <tr>

                        <th colspan="3" class="text-right">
                            Total
                        </th>

                        <th>
                            ${{ number_format($order->estimated_cost, 2) }}
                        </th>

                    </tr>

                </tfoot>

            </table>

        </section>

    </div>

</section>

@endsection