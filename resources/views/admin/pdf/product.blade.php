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

    .section {
        margin-top: 18px;
    }

    .section-title {
        background: #efefef;
        border: 1px solid #000;
        padding: 7px;
        font-weight: bold;
    }

    .grid th {
        background: #efefef;
        border: 1px solid #000;
    }

    .grid td {
        border: 1px solid #000;
    }

    .resume td {
        border: 1px solid #000;
        text-align: center;
        padding: 8px;
    }

    .resume strong {
        font-size: 17px;
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

<table>

<tr>

    <td width="18%" class="border text-center">

        <img
            style="width: 150px; margin: 20px auto;"
            src="{{ url('img/simonel.png') }}"
            alt=""
        >

    </td>

    <td width="57%" class="border text-center">

        <div class="title">
            SIMONEL
        </div>

        <div class="subtitle">
            REPORTE DE PRODUCTO
        </div>

        <br>

        <strong>
            {{ $product['name'] ?? $product->name }}
        </strong>

        <br><br>

        Periodo:

        <strong>
            {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
        </strong>

        al

        <strong>
            {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        </strong>

    </td>

    <td width="25%">

        <table class="border">

            <tr>
                <td class="text-center">
                    <strong>PRODUCTO</strong>
                </td>
            </tr>

            <tr>
                <td class="text-center">
                    #{{ $product['id'] ?? $product->id }}
                </td>
            </tr>

            <tr>
                <td class="text-center">
                    {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>

        </table>

    </td>

</tr>

</table>

{{-- =====================================================
RESUMEN
===================================================== --}}

@php

$productName = $product['name'] ?? $product->name;

$salesCount = $product['sales_count'] ?? $product->sales_count;

$quantity = $product['quantity'] ?? $product->quantity;

$total = $product['total'] ?? $product->total;

@endphp

<div class="section">

<div class="section-title">
    RESUMEN DEL PRODUCTO
</div>

<table class="resume">

    <tr>

        <td width="33%">

            <strong>
                {{ $salesCount }}
            </strong>

            <br>

            Ventas

        </td>

        <td width="33%">

            <strong>
                {{ number_format($quantity, 2) }}
            </strong>

            <br>

            Cantidad vendida

        </td>

        <td width="34%">

            <strong>
                $ {{ number_format($total, 2) }}
            </strong>

            <br>

            Total vendido

        </td>

    </tr>

</table>

</div>

{{-- =====================================================
DETALLE
===================================================== --}}

<div class="section">

<div class="section-title">
    DETALLE DE VENTAS
</div>

<table class="grid">

    <thead>

        <tr>

            <th width="12%">
                Fecha
            </th>

            <th width="10%">
                Folio
            </th>

            <th width="23%">
                Cliente
            </th>

            <th width="15%">
                Vendedor
            </th>

            <th width="12%">
                Cantidad
            </th>

            <th width="13%">
                Precio
            </th>

            <th width="15%">
                Importe
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($sales as $sale)

        @php

            $item = $sale->products
                ->firstWhere('product_id', $product['id'] ?? $product->id);

        @endphp

        @if($item)

        <tr>

            <td class="text-center">

                {{ $sale->created_at->format('d/m/Y') }}

            </td>

            <td class="text-center">

                #{{ $sale->id }}

            </td>

            <td>

                {{ optional($sale->user->customer)->business_name
                    ?? optional($sale->customer)->name
                    ?? 'Público General' }}

            </td>

            <td>

                {{ optional($sale->seller)->name }}

                {{ optional($sale->seller)->last_name }}

            </td>

            <td class="text-right">

                {{ number_format($item->quantity, 2) }}

            </td>

            <td class="text-right">

                $ {{ number_format($item->base_price, 2) }}

            </td>

            <td class="text-right">

                $ {{ number_format($item->total_with_iva, 2) }}

            </td>

        </tr>

        @endif

    @empty

        <tr>

            <td colspan="7" class="text-center">

                No existen ventas para este producto.

            </td>

        </tr>

    @endforelse

    </tbody>

    <tfoot>

        <tr>

            <th colspan="4" class="text-right">
                TOTAL
            </th>

            <th class="text-right">

                {{ number_format($quantity, 2) }}

            </th>

            <th></th>

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

Producto:
{{ $productName }}

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
