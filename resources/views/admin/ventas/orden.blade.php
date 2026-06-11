@extends('layout.dashboard-master')

@section('title', 'Orden de Venta')
@section('tab_title', 'Orden de Venta | ' . config('app.name'))
@section('description', 'Detalle de la orden de venta.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">

    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Orden de Venta #{{ $sale->id }}
        </h1>
    </div>

    <div class="fluid-container mb-16">

        <p class="mb-12">
            @include('components.alert')

            <span class="color-link">«</span>

            <a href="{{ url('admin/ventas') }}">
                Ver todas las ventas
            </a>
        </p>

        <div class="md:row mb-8">

            <div class="md:col-1/3">
                <section class="db-panel text-center">
                    <h5>Total Productos</h5>

                    <h2>
                        {{ $sale->products->count() }}
                    </h2>
                </section>
            </div>

            <div class="md:col-1/3">
                <section class="db-panel text-center">
                    <h5>Cantidad Vendida</h5>

                    <h2>
                        {{ number_format($sale->products->sum('quantity'),3) }}
                    </h2>
                </section>
            </div>

            <div class="md:col-1/3">
                <section class="db-panel text-center">
                    <h5>Total Venta</h5>

                    <h2>
                        $ {{ number_format($sale->total_with_iva,2) }}
                    </h2>
                </section>
            </div>

        </div>

        <section class="db-panel mb-8">

            <h3 class="db-panel__title">
                Datos Generales
            </h3>

            <table class="order-table__details">

                <tbody>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Folio:
                        </th>

                        <td class="order-table__cell">
                            #{{ $sale->id }}
                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Cliente:
                        </th>

                        <td class="order-table__cell">
                            {{ $sale->user->name }}
                            {{ $sale->user->last_name }}
                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Fecha:
                        </th>

                        <td class="order-table__cell">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Estado:
                        </th>

                        <td class="order-table__cell">

                            <span class="badge badge-status badge-{{ $sale->status }}">
                                {{ $status[$sale->status] ?? $sale->status }}
                            </span>

                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Subtotal:
                        </th>

                        <td class="order-table__cell">
                            $ {{ number_format($sale->total_sale_price,4) }}
                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            IVA:
                        </th>

                        <td class="order-table__cell">
                            $ {{ number_format($sale->iva,4) }}
                        </td>
                    </tr>

                    <tr class="order-table__row">
                        <th class="order-table__cell--heading">
                            Total:
                        </th>

                        <td class="order-table__cell">
                            <strong>
                                $ {{ number_format($sale->total_with_iva,4) }}
                            </strong>
                        </td>
                    </tr>

                </tbody>

            </table>

        </section>
        <section class="db-panel">

            <h3 class="db-panel__title">
                Productos Vendidos
            </h3>

            <table class="table size-caption mx-auto md:table--responsive">

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Descuento</th>
                        <th>IVA</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($sale->products as $saleProduct)

                        <tr>

                            <td>
                                {{ $saleProduct->product->name ?? 'Producto eliminado' }}
                            </td>

                            <td>
                                {{ $saleProduct->product->type->name ?? 'Sin categoría' }}
                            </td>

                            <td>
                                {{ number_format($saleProduct->quantity,3) }}
                            </td>

                            <td>
                                $ {{ number_format($saleProduct->base_price,4) }}
                            </td>

                            <td>
                                {{ number_format($saleProduct->discount,2) }} %
                            </td>

                            <td>
                                {{ number_format($saleProduct->iva,2) }} %
                            </td>

                            <td>
                                $ {{ number_format($saleProduct->subtotal,4) }}
                            </td>

                            <td>
                                <strong>
                                    $ {{ number_format($saleProduct->total_with_iva,4) }}
                                </strong>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </section>

        @if(!empty($sale->comment))

            <section class="db-panel mt-8">

                <h3 class="db-panel__title">
                    Comentarios
                </h3>

                <p>
                    {{ $sale->comment }}
                </p>

            </section>

        @endif
        
        @if($sale->status != 'paid')
            <section class="mt-8">

                <order-status-form
                    action="{{ url('admin/ventas/orden/' . $sale->id . '/actualizar') }}"
                    method="PUT"
                    :status="{{ $status }}"
                    :sale-data="{{ $sale }}"
                    :payment="3"
                    :min-payment="0"
                    :payments-data="{{ $payments }}"
                    :assigned-payments="{{ $sale->payments }}"
                >
                </order-status-form>

            </section>

        @endif

    </div>

</section>

@endsection