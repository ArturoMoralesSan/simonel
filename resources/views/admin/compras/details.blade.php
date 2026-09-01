@extends('layout.dashboard-master')

@section('title', 'Detalle compra')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Compra #{{ $purchase->id }}
    </h1>
</div>

<div class="fluid-container">

    <p class="mb-12">
        @include('components.alert')

        <span class="color-link">«</span>
        <a href="{{ url('admin/compras/') }}">
            Ver todas las compras
        </a>
    </p>


    {{-- INFORMACIÓN GENERAL --}}

    <section class="db-panel mb-8">

        <h3 class="db-panel__title">
            Información general
        </h3>

        <table class="table">

            <tbody>

                <tr>
                    <th>Proveedor</th>
                    <td>
                        {{ optional($purchase->supplier)->business_name }}
                    </td>
                </tr>

                <tr>
                    <th>Factura</th>
                    <td>
                        {{ $purchase->invoice_number ?: '-' }}
                    </td>
                </tr>

                <tr>
                    <th>Fecha</th>
                    <td>
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Total</th>
                    <td>
                        ${{ number_format($purchase->total, 2) }}
                    </td>
                </tr>

                <tr>
                    <th>Notas</th>
                    <td>
                        {{ $purchase->notes ?: '-' }}
                    </td>
                </tr>

            </tbody>

        </table>

    </section>


    {{-- MATERIAS PRIMAS --}}

    <section class="db-panel mb-8">

        <h3 class="db-panel__title">
            Materias primas
        </h3>

        <table class="table">

            <thead>

                <tr>
                    <th>Materia prima</th>
                    <th>Cantidad</th>
                    <th>Costo unitario</th>
                    <th>Total</th>
                    <th>Almacén</th>
                    <th>Lote</th>
                    <th>Caducidad</th>
                </tr>

            </thead>

            <tbody>

                @foreach($purchase->items as $item)

                    @php
                        $lot = $purchase->lots
                            ->where('raw_material_id', $item->raw_material_id)
                            ->first();
                    @endphp

                    <tr>

                        <td>
                            {{ optional($item->rawMaterial)->name }}
                        </td>

                        <td>
                            {{ $item->quantity }}
                        </td>

                        <td>
                            ${{ number_format($item->unit_cost, 2) }}
                        </td>

                        <td>
                            ${{ number_format($item->total_cost, 2) }}
                        </td>

                        <td>
                            {{ optional(optional($lot)->warehouse)->name ?: '-' }}
                        </td>

                        <td>
                            {{ optional($lot)->lot_number ?: '-' }}
                        </td>

                        <td>
                            {{ optional($lot)->formated_expiration_date ?: '-' }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </section>


    {{-- DESHUESE --}}

    @if($purchase->boning)

        <section class="db-panel mb-8">

            <h3 class="db-panel__title">
                Deshuese del combo
            </h3>

            <div class="row">

                {{-- PIERNA --}}

                <div class="md:col-1/2">

                    <h4>
                        PIERNA
                    </h4>

                    <table class="table">

                        <tbody>

                            <tr>
                                <th>Pulpa</th>
                                <td>
                                    {{ $purchase->boning->pulpa ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Hueso</th>
                                <td>
                                    {{ $purchase->boning->hueso ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Lonja</th>
                                <td>
                                    {{ $purchase->boning->lonja ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Cuero planchado</th>
                                <td>
                                    {{ $purchase->boning->cuero_planchar ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Chamorro</th>
                                <td>
                                    {{ $purchase->boning->chamorro ?? '0.00' }} %
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>


                {{-- CANAL --}}

                <div class="md:col-1/2">

                    <h4>
                        CANAL
                    </h4>

                    <table class="table">

                        <tbody>

                            <tr>
                                <th>Costilla</th>
                                <td>
                                    {{ $purchase->boning->costilla ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Piernas</th>
                                <td>
                                    {{ $purchase->boning->piernas ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Paleta</th>
                                <td>
                                    {{ $purchase->boning->paleta ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Lomo</th>
                                <td>
                                    {{ $purchase->boning->lomo ?? '0.00' }} %
                                </td>
                            </tr>

                            <tr>
                                <th>Espinazo</th>
                                <td>
                                    {{ $purchase->boning->espinazo ?? '0.00' }} %
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    @endif

</div>

@endsection
