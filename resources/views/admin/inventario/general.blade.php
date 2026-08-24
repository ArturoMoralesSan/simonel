@extends('layout.dashboard-master')

@section('title', 'Inventario general')

@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">

    <h1 class="dashboard-heading__title">
        Inventario general
    </h1>

    <p>
        Inventario disponible agrupado por almacén
    </p>

</div>

<div class="fluid-container">

    {{-- ==========================================================
        RESUMEN GENERAL
    =========================================================== --}}

    @php
        $totalQuantity = $inventory->sum('quantity');
        $totalValue = $inventory->sum('total');
    @endphp

    <section class="db-panel">

        <div class="row">

            <div class="md:col-1/3">

                <strong>Almacenes</strong>

                <p style="font-size: 22px;">
                    {{ $inventory->count() }}
                </p>

            </div>

            <div class="md:col-1/3">

                <strong>Cantidad total en kilos</strong>

                <p style="font-size: 22px;">
                    {{ number_format($totalQuantity, 3) }} kg
                </p>

            </div>

            <div class="md:col-1/3">

                <strong>Valor total</strong>

                <p style="font-size: 22px;">
                    ${{ number_format($totalValue, 2) }}
                </p>

            </div>

        </div>

    </section>


    {{-- ==========================================================
        ALMACENES
    =========================================================== --}}

    @forelse($inventory as $warehouse)

        {{-- ======================================================
            ENCABEZADO DEL ALMACÉN
        ======================================================= --}}

        <section
            class="db-panel"
            style="margin-top: 20px;"
        >

            <div class="row">

                <div class="md:col-1/2">

                    <h2 style="margin-bottom: 5px;">
                        {{ $warehouse['name'] }}
                    </h2>

                    <span class="badge badge-info">
                        {{ $warehouse['type'] }}
                    </span>

                </div>

                <div class="md:col-1/4">

                    <strong>
                        Cantidad total
                    </strong>

                    <p style="font-size: 20px;">
                        {{ number_format($warehouse['quantity'], 3) }} kg
                    </p>

                </div>

                <div class="md:col-1/4">

                    <strong>
                        Valor del inventario
                    </strong>

                    <p style="font-size: 20px;">
                        ${{ number_format($warehouse['total'], 2) }}
                    </p>

                </div>

            </div>

        </section>


        {{-- ======================================================
            PRODUCTOS TERMINADOS
        ======================================================= --}}

        @if(count($warehouse['products']) > 0)

            <section
                class="db-panel"
                style="margin-top: 20px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Productos terminados
                    </h2>

                </div>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    Producto
                                </th>

                                <th>
                                    Descripción
                                </th>

                                <th>
                                    Cantidad (kg)
                                </th>

                                <th>
                                    Costo unitario
                                </th>

                                <th>
                                    Valor
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($warehouse['products'] as $product)

                                {{-- ==========================================
                                    PRODUCTO
                                =========================================== --}}

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $product['name'] }}
                                        </strong>

                                    </td>

                                    <td>

                                        <span class="description">
                                            {{ $product['description'] }}
                                        </span>

                                    </td>

                                    <td>

                                        {{ number_format(
                                            $product['quantity'],
                                            3
                                        ) }}
                                        kg

                                    </td>

                                    <td>

                                        ${{ number_format(
                                            $product['quantity'] > 0
                                                ? $product['total'] / $product['quantity']
                                                : 0,
                                            2
                                        ) }}

                                    </td>

                                    <td>

                                        <strong>
                                            ${{ number_format(
                                                $product['total'],
                                                2
                                            ) }}
                                        </strong>

                                    </td>

                                </tr>


                                {{-- ==========================================
                                    LOTES DEL PRODUCTO
                                =========================================== --}}

                                <tr>

                                    <td
                                        colspan="5"
                                        style="padding: 0;"
                                    >

                                        <div
                                            style="
                                                padding: 15px 25px;
                                                background: rgba(0,0,0,.02);
                                            "
                                        >

                                            <div class="table-responsive">

                                                <table class="table">

                                                    <thead>

                                                        <tr>

                                                            <th>
                                                                Lote
                                                            </th>

                                                            <th>
                                                                Orden de producción
                                                            </th>

                                                            <th>
                                                                Producción
                                                            </th>

                                                            <th>
                                                                Caducidad
                                                            </th>

                                                            <th>
                                                                Cantidad (kg)
                                                            </th>

                                                            <th>
                                                                Costo
                                                            </th>

                                                            <th>
                                                                Total
                                                            </th>

                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        @foreach($product['lots'] as $lot)

                                                            <tr>

                                                                <td>
                                                                    {{ $lot['lot_number'] }}
                                                                </td>

                                                                <td>

                                                                    {{ $lot['order_number'] ?? 'Sin orden de producción' }}

                                                                </td>

                                                                <td>

                                                                    @if($lot['production_date'])

                                                                        {{ \Carbon\Carbon::parse(
                                                                            $lot['production_date']
                                                                        )->format('d/m/Y') }}

                                                                    @else

                                                                        -

                                                                    @endif

                                                                </td>

                                                                <td>

                                                                    @if($lot['expiration_date'])

                                                                        @if($lot['is_expired'])

                                                                            <span
                                                                                style="
                                                                                    color: red;
                                                                                    font-weight: bold;
                                                                                "
                                                                            >

                                                                                {{ \Carbon\Carbon::parse(
                                                                                    $lot['expiration_date']
                                                                                )->format('d/m/Y') }}

                                                                                (Vencido)

                                                                            </span>

                                                                        @else

                                                                            {{ \Carbon\Carbon::parse(
                                                                                $lot['expiration_date']
                                                                            )->format('d/m/Y') }}

                                                                        @endif

                                                                    @else

                                                                        Sin caducidad

                                                                    @endif

                                                                </td>

                                                                <td>

                                                                    {{ number_format(
                                                                        $lot['quantity'],
                                                                        3
                                                                    ) }}
                                                                    kg

                                                                </td>

                                                                <td>

                                                                    ${{ number_format(
                                                                        $lot['cost'],
                                                                        2
                                                                    ) }}

                                                                </td>

                                                                <td>

                                                                    ${{ number_format(
                                                                        $lot['total'],
                                                                        2
                                                                    ) }}

                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </section>

        @endif


        {{-- ======================================================
            MATERIAS PRIMAS
        ======================================================= --}}

        @if(count($warehouse['raw_materials']) > 0)

            <section
                class="db-panel"
                style="margin-top: 20px;"
            >

                <div class="dashboard-heading">

                    <h2 class="dashboard-heading__title">
                        Materias primas
                    </h2>

                </div>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    Materia prima
                                </th>

                                <th>
                                    Descripción
                                </th>

                                <th>
                                    Cantidad (kg)
                                </th>

                                <th>
                                    Costo unitario
                                </th>

                                <th>
                                    Valor
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($warehouse['raw_materials'] as $material)

                                {{-- ==========================================
                                    MATERIA PRIMA
                                =========================================== --}}

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $material['name'] }}
                                        </strong>

                                    </td>

                                    <td>

                                        <span class="description">
                                            {{ $material['description'] }}
                                        </span>

                                    </td>

                                    <td>

                                        {{ number_format(
                                            $material['quantity'],
                                            3
                                        ) }}
                                        kg

                                    </td>

                                    <td>

                                        ${{ number_format(
                                            $material['cost'],
                                            2
                                        ) }}

                                    </td>

                                    <td>

                                        <strong>
                                            ${{ number_format(
                                                $material['total'],
                                                2
                                            ) }}
                                        </strong>

                                    </td>

                                </tr>


                                {{-- ==========================================
                                    LOTES DE MATERIA PRIMA
                                =========================================== --}}

                                <tr>

                                    <td
                                        colspan="5"
                                        style="padding: 0;"
                                    >

                                        <div
                                            style="
                                                padding: 15px 25px;
                                                background: rgba(0,0,0,.02);
                                            "
                                        >

                                            <div class="table-responsive">

                                                <table class="table">

                                                    <thead>

                                                        <tr>

                                                            <th>
                                                                Lote
                                                            </th>

                                                            <th>
                                                                Entrada
                                                            </th>

                                                            <th>
                                                                Caducidad
                                                            </th>

                                                            <th>
                                                                Cantidad (kg)
                                                            </th>

                                                            <th>
                                                                Costo
                                                            </th>

                                                            <th>
                                                                Total
                                                            </th>

                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        @foreach($material['lots'] as $lot)

                                                            <tr>

                                                                <td>
                                                                    {{ $lot['lot_number'] }}
                                                                </td>

                                                                <td>

                                                                    @if($lot['entry_date'])

                                                                        {{ \Carbon\Carbon::parse(
                                                                            $lot['entry_date']
                                                                        )->format('d/m/Y') }}

                                                                    @else

                                                                        -

                                                                    @endif

                                                                </td>

                                                                <td>

                                                                    @if($lot['expiration_date'])

                                                                        @if($lot['is_expired'])

                                                                            <span
                                                                                style="
                                                                                    color: red;
                                                                                    font-weight: bold;
                                                                                "
                                                                            >

                                                                                {{ \Carbon\Carbon::parse(
                                                                                    $lot['expiration_date']
                                                                                )->format('d/m/Y') }}

                                                                                (Vencido)

                                                                            </span>

                                                                        @else

                                                                            {{ \Carbon\Carbon::parse(
                                                                                $lot['expiration_date']
                                                                            )->format('d/m/Y') }}

                                                                        @endif

                                                                    @else

                                                                        Sin caducidad

                                                                    @endif

                                                                </td>

                                                                <td>

                                                                    {{ number_format(
                                                                        $lot['quantity'],
                                                                        3
                                                                    ) }}
                                                                    kg

                                                                </td>

                                                                <td>

                                                                    ${{ number_format(
                                                                        $lot['cost'],
                                                                        2
                                                                    ) }}

                                                                </td>

                                                                <td>

                                                                    ${{ number_format(
                                                                        $lot['total'],
                                                                        2
                                                                    ) }}

                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </section>

        @endif


        {{-- ======================================================
            ALMACÉN SIN INVENTARIO
        ======================================================= --}}

        @if(
            count($warehouse['products']) === 0 &&
            count($warehouse['raw_materials']) === 0
        )

            <section
                class="db-panel"
                style="margin-top: 20px;"
            >

                <div
                    style="
                        text-align: center;
                        padding: 30px;
                    "
                >

                    <strong>
                        Este almacén no tiene inventario disponible.
                    </strong>

                </div>

            </section>

        @endif

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
                    No existen lotes disponibles en los almacenes activos.
                </p>

            </div>

        </section>

    @endforelse

</div>

@endsection