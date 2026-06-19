@extends('layout.dashboard-master')

@section('title', 'Detalle compra')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Compra #{{ $purchase->id }}
    </h1>
</div>

<div class="fluid-container">

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
                        {{ $purchase->invoice_number }}
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

    <section class="db-panel">

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
                        {{ optional(optional($lot)->warehouse)->name }}
                    </td>

                    <td>
                        {{ optional($lot)->lot_number }}
                    </td>

                    <td>
                        {{ optional($lot)->formated_expiration_date }}
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </section>

</div>

@endsection