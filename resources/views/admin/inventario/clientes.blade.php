@extends('layout.dashboard-master')

@section('title', 'Inventario por clientes')

@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">

    <h1 class="dashboard-heading__title">
        Inventario por clientes
    </h1>

    <p>
        Inventario, movimientos y productos pendientes agrupados por cliente
    </p>

</div>

<div class="fluid-container">

    {{-- ==========================================================
        RESUMEN GENERAL
    =========================================================== --}}

    @php

        $totalClients = $clients->count();

        $totalQuantity = $clients->sum(function ($client) {

            return collect(
                $client['inventory'] ?? []
            )->sum(function ($item) {

                return (float) (
                    $item->quantity ?? 0
                );

            });

        });

    @endphp


    <section class="db-panel">

        <div class="row">

            <div class="md:col-1/2">

                <strong>
                    Clientes
                </strong>

                <p style="font-size: 22px;">
                    {{ $totalClients }}
                </p>

            </div>


            <div class="md:col-1/2">

                <strong>
                    Cantidad total
                </strong>

                <p style="font-size: 22px;">
                    {{ number_format(
                        $totalQuantity,
                        3
                    ) }}
                    kg
                </p>

            </div>

        </div>

    </section>


    {{-- ==========================================================
        CLIENTES
    =========================================================== --}}

    @forelse($clients as $client)

        @php

            $inventory = collect(
                $client['inventory'] ?? []
            );

            $entradas = collect(
                $client['movementsEntradas'] ?? []
            );

            $salidas = collect(
                $client['movementsSalidas'] ?? []
            );

            $mermas = collect(
                $client['movementsMermas'] ?? []
            );

            $pendingProducts = collect(
                $client['pendingProducts'] ?? []
            );

            $pendingProductsData = collect(
                $client['pendingProductsData'] ?? []
            );

            $clientQuantity = $inventory->sum(
                function ($item) {

                    return (float) (
                        $item->quantity ?? 0
                    );

                }
            );

            $totalEntradas = $entradas->sum(
                function ($movement) {

                    return (float) (
                        $movement->quantity ?? 0
                    );

                }
            );

            $totalSalidas = $salidas->sum(
                function ($movement) {

                    return (float) (
                        $movement->quantity ?? 0
                    );

                }
            );

            $totalMermas = $mermas->sum(
                function ($movement) {

                    return (float) (
                        $movement->quantity ?? 0
                    );

                }
            );

        @endphp


        {{-- ======================================================
            CARD DEL CLIENTE
        ======================================================= --}}

        <section
            class="db-panel client-inventory-card"
            style="margin-top: 20px;"
        >

            {{-- ==================================================
                ENCABEZADO DEL CLIENTE
            =================================================== --}}

            <div class="row">

                <div class="md:col-1/2">

                    <h2 style="margin-bottom: 5px;">

                        {{ $client['name'] }}

                    </h2>


                    @if($client['customer_type'])

                        <span class="badge badge-info">

                            {{ $client['customer_type'] }}

                        </span>

                    @endif

                </div>


                <div class="md:col-1/2">

                    <strong>
                        Cantidad en inventario
                    </strong>

                    <p style="font-size: 20px;">

                        {{ number_format(
                            $clientQuantity,
                            3
                        ) }}

                        kg

                    </p>

                </div>

            </div>


            {{-- ==================================================
                INVENTARIO ACTUAL
            =================================================== --}}

            <div
                class="client-section"
                style="margin-top: 25px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Inventario actual
                    </h2>

                </div>


                @if($inventory->count() > 0)

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($inventory as $item)

                                    <tr>

                                        <td>

                                            <strong>

                                                {{ optional(
                                                    optional(
                                                        $item->product
                                                    )->manufactured
                                                )->name
                                                    ?? 'Producto eliminado' }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ number_format(
                                                (float) (
                                                    $item->quantity ?? 0
                                                ),
                                                3
                                            ) }}

                                            kg

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="client-empty-message">

                        No hay inventario disponible.

                    </div>

                @endif

            </div>


            {{-- ==================================================
                CÁMARA DE REFRIGERACIÓN
            =================================================== --}}

            <div
                class="client-section"
                style="margin-top: 30px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Cámara de refrigeración
                    </h2>

                </div>


                @if($entradas->count() > 0)

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Referencia
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($entradas as $movement)

                                    <tr>

                                        <td>

                                            <strong>

                                                {{ optional(
                                                    optional(
                                                        optional(
                                                            $movement->inventory
                                                        )->product
                                                    )->manufactured
                                                )->name
                                                    ?? 'Producto eliminado' }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ number_format(
                                                (float) (
                                                    $movement->quantity ?? 0
                                                ),
                                                3
                                            ) }}

                                            kg

                                        </td>


                                        <td>

                                            @if($movement->created_at)

                                                {{ \Carbon\Carbon::parse(
                                                    $movement->created_at
                                                )->format(
                                                    'd/m/Y H:i'
                                                ) }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        <td>

                                            {{ $movement->reference
                                                ?? $movement->description
                                                ?? '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="client-empty-message">

                        No hay movimientos en cámara de refrigeración.

                    </div>

                @endif

            </div>


            {{-- ==================================================
                VENTA EN PISO
            =================================================== --}}

            <div
                class="client-section"
                style="margin-top: 30px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Venta en piso
                    </h2>

                </div>


                @if($salidas->count() > 0)

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Referencia
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($salidas as $movement)

                                    <tr>

                                        <td>

                                            <strong>

                                                {{ optional(
                                                    optional(
                                                        optional(
                                                            $movement->inventory
                                                        )->product
                                                    )->manufactured
                                                )->name
                                                    ?? 'Producto eliminado' }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ number_format(
                                                (float) (
                                                    $movement->quantity ?? 0
                                                ),
                                                3
                                            ) }}

                                            kg

                                        </td>


                                        <td>

                                            @if($movement->created_at)

                                                {{ \Carbon\Carbon::parse(
                                                    $movement->created_at
                                                )->format(
                                                    'd/m/Y H:i'
                                                ) }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        <td>

                                            {{ $movement->reference
                                                ?? $movement->description
                                                ?? '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="client-empty-message">

                        No hay ventas en piso registradas.

                    </div>

                @endif

            </div>


            {{-- ==================================================
                MERMAS
            =================================================== --}}

            <div
                class="client-section"
                style="margin-top: 30px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Mermas
                    </h2>

                </div>


                @if($mermas->count() > 0)

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Motivo
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($mermas as $movement)

                                    <tr>

                                        <td>

                                            <strong>

                                                {{ optional(
                                                    optional(
                                                        optional(
                                                            $movement->inventory
                                                        )->product
                                                    )->manufactured
                                                )->name
                                                    ?? 'Producto eliminado' }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ number_format(
                                                (float) (
                                                    $movement->quantity ?? 0
                                                ),
                                                3
                                            ) }}

                                            kg

                                        </td>


                                        <td>

                                            @if($movement->created_at)

                                                {{ \Carbon\Carbon::parse(
                                                    $movement->created_at
                                                )->format(
                                                    'd/m/Y H:i'
                                                ) }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        <td>

                                            {{ $movement->description
                                                ?? $movement->reference
                                                ?? '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="client-empty-message">

                        No hay mermas registradas.

                    </div>

                @endif

            </div>


            {{-- ==================================================
                PRODUCTOS PENDIENTES
            =================================================== --}}

            <div
                class="client-section"
                style="margin-top: 30px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Productos pendientes
                    </h2>

                </div>


                @if($pendingProducts->count() > 0)

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Venta
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach(
                                    $pendingProducts as
                                    $productId => $productName
                                )

                                    @php

                                        $pending =
                                            $pendingProductsData
                                                ->get($productId);

                                    @endphp

                                    <tr>

                                        <td>

                                            <strong>
                                                {{ $productName }}
                                            </strong>

                                        </td>


                                        <td>

                                            @if($pending)

                                                Venta #{{ $pending->sale_id }}

                                            @else

                                                -

                                            @endif

                                        </td>


                                        <td>

                                            @if($pending)

                                                {{ number_format(
                                                    (float) (
                                                        $pending->quantity
                                                        ?? 0
                                                    ),
                                                    3
                                                ) }}

                                                kg

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="client-empty-message">

                        No hay productos pendientes.

                    </div>

                @endif

            </div>


            {{-- ==================================================
                RESUMEN DE MOVIMIENTOS
            =================================================== --}}

            <div
                class="client-movement-summary"
                style="margin-top: 30px;"
            >

                <div class="row">

                    <div class="md:col-1/3">

                        <strong>
                            Cámara de refrigeración
                        </strong>

                        <p style="font-size: 20px;">

                            {{ number_format(
                                $totalEntradas,
                                3
                            ) }}

                            kg

                        </p>

                    </div>


                    <div class="md:col-1/3">

                        <strong>
                            Venta en piso
                        </strong>

                        <p style="font-size: 20px;">

                            {{ number_format(
                                $totalSalidas,
                                3
                            ) }}

                            kg

                        </p>

                    </div>


                    <div class="md:col-1/3">

                        <strong>
                            Mermas
                        </strong>

                        <p style="font-size: 20px;">

                            {{ number_format(
                                $totalMermas,
                                3
                            ) }}

                            kg

                        </p>

                    </div>

                </div>

            </div>

        </section>

    @empty

        <section
            class="db-panel"
            style="margin-top: 20px;"
        >

            <div
                style="
                    text-align: center;
                    padding: 40px;
                "
            >

                <strong>
                    No hay inventario disponible.
                </strong>

                <p>
                    No existen clientes con inventario registrado.
                </p>

            </div>

        </section>

    @endforelse

</div>


{{-- ==============================================================
    ESTILOS
================================================================ --}}

@push('styles')

<style>

    .client-inventory-card {

        border-radius: 12px;

        overflow: hidden;

    }


    .client-section {

        border-top:
            1px solid
            rgba(0, 0, 0, .08);

        padding-top: 20px;

    }


    .client-section
    .dashboard-heading {

        margin-bottom: 15px;

    }


    .client-section
    .dashboard-heading__title {

        font-size: 18px;

    }


    .client-section table {

        margin-bottom: 0;

    }


    .client-section
    table thead th {

        background:
            rgba(0, 0, 0, .025);

    }


    .client-movement-summary {

        border-top:
            1px solid
            rgba(0, 0, 0, .08);

        padding-top: 20px;

    }


    .client-empty-message {

        text-align: center;

        padding: 20px;

        border-radius: 8px;

        background:
            rgba(0, 0, 0, .025);

        opacity: .7;

    }

</style>

@endpush

@endsection