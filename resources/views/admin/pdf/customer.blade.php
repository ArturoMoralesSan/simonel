<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<style>

    @page {
        margin: 12mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #222;
        margin: 0;
        padding: 0;
    }

    .page {
        width: 94%;
        margin: 0 auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 5px;
        vertical-align: top;
    }

    .border {
        border: 1px solid #000;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .title {
        font-size: 22px;
        font-weight: bold;
    }

    .subtitle {
        font-size: 13px;
        font-weight: bold;
    }

    .small {
        font-size: 10px;
        line-height: 14px;
    }

    .header {
        margin-bottom: 15px;
    }

    .section {
        margin-top: 15px;
    }

    .section-title {
        background: #efefef;
        border: 1px solid #000;
        padding: 6px;
        font-weight: bold;
    }

    .resume {
        margin-top: 10px;
    }

    .resume td {
        border: 1px solid #000;
        text-align: center;
        padding: 8px;
    }

    .resume strong {
        font-size: 16px;
    }

    .sales {
        margin-top: 10px;
    }

    .sales th {
        background: #efefef;
        border: 1px solid #000;
    }

    .sales td {
        border: 1px solid #000;
    }

    .footer {
        margin-top: 25px;
        text-align: center;
        font-size: 9px;
        color: #666;
    }

</style>

</head>

<body>

<div class="page">

{{-- =====================================================
ENCABEZADO
===================================================== --}}



{{-- =====================================================
RESUMEN
===================================================== --}}

@php

$salesCount = $summary->sales_count ?? 0;

$total = $summary->total ?? 0;

@endphp

<table class="resume">

<tr>

    <td width="50%">

        <strong>
            {{ $salesCount }}
        </strong>

        <br>

        Compras

    </td>

    <td width="50%">

        <strong>
            $ {{ number_format($total, 2) }}
        </strong>

        <br>

        Total comprado

    </td>

</tr>

</table>

{{-- =====================================================
DATOS DEL CLIENTE
===================================================== --}}

<div class="section">

<div class="section-title">
    DATOS DEL CLIENTE
</div>

<table style="margin-top:10px;">

    <tr>

        <td width="20%" class="border">

            <strong>
                NOMBRE
            </strong>

        </td>

        <td width="80%" class="border">

            {{ $customer->business_name ?? 'Público General' }}

        </td>

    </tr>

    @if($customer->street || $customer->ext_number)

    <tr>

        <td class="border">

            <strong>
                DIRECCIÓN
            </strong>

        </td>

        <td class="border">

            {{ $customer->street }}
            #{{ $customer->ext_number }}

            @if($customer->population)
                , {{ $customer->population }}
            @endif

            @if($customer->postal_code)
                {{ $customer->postal_code }}
            @endif

        </td>

    </tr>

    @endif

    @if($customer->state)

    <tr>

        <td class="border">

            <strong>
                CIUDAD / ESTADO
            </strong>

        </td>

        <td class="border">

            {{ $customer->state }}

        </td>

    </tr>

    @endif

</table>

</div>

{{-- =====================================================
DETALLE
===================================================== --}}

<div class="section">

<div class="section-title">
    DETALLE DE COMPRAS
</div>

<table class="sales">

    <thead>

        <tr>

            <th width="11%">
                Fecha
            </th>

            <th width="9%">
                Folio
            </th>

            <th width="17%">
                Vendedor
            </th>

            <th width="38%">
                Productos
            </th>

            <th width="12%">
                Pago
            </th>

            <th width="13%">
                Total
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($sales as $sale)

        <tr>

            <td class="text-center">

                {{ $sale->created_at->format('d/m/Y') }}

            </td>

            <td class="text-center">

                #{{ $sale->id }}

            </td>

            <td>

                {{ optional($sale->seller)->name }}
                {{ optional($sale->seller)->last_name }}

            </td>

            <td>

                @foreach($sale->products as $item)

                    {{ optional(optional($item->product)->manufactured)->name }}

                    &nbsp;

                    ({{ number_format($item->quantity, 2) }})

                    @if(!$loop->last)
                        <br>
                    @endif

                @endforeach

            </td>

            <td>

                @foreach($sale->payments as $payment)

                    {{ $payment->name }}

                    ${{ number_format($payment->pivot->cost, 2) }}

                    @if(!$loop->last)
                        <br>
                    @endif

                @endforeach

            </td>

            <td class="text-right">

                $ {{ number_format($sale->total_with_iva, 2) }}

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="6" class="text-center">

                No existen compras para este cliente
                durante el periodo seleccionado.

            </td>

        </tr>

    @endforelse

    </tbody>

    <tfoot>

        <tr>

            <th colspan="5" class="text-right">

                TOTAL

            </th>

            <th class="text-right">

                $ {{ number_format($total, 2) }}

            </th>

        </tr>

    </tfoot>

</table>

</div>

{{-- =====================================================
PIE
===================================================== --}}

<div class="footer">

Documento generado automáticamente por el ERP SIMONEL.

<br>

Cliente:

{{ $customer->business_name ?? 'Público General' }}

<br>

Periodo:

{{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}

-

{{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}

<br>

Generado el {{ now()->format('d/m/Y H:i:s') }}

</div>

</div>

</body>

</html>
