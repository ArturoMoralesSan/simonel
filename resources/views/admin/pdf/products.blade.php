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

    .section {
        margin-top: 18px;
    }

    .section-title {
        background: #efefef;
        border: 1px solid #000;
        padding: 7px;
        font-weight: bold;
        font-size: 12px;
    }

    .grid {
        width: 100%;
        border-collapse: collapse;
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
        font-size: 16px;
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
            style="width: 150px; display: block; margin: 20px auto;"
            src="{{ url('img/simonel.png') }}"
            alt=""
        >

    </td>

    <td width="57%" class="border text-center">

        <div class="title">
            SIMONEL
        </div>

        <div class="subtitle">
            PROFR. SIMON MOLINA MALDONADO
        </div>

        <br>

        <strong>
            REPORTE DE PRODUCTOS
        </strong>

        <br><br>

        <div class="small">

            Periodo:

            <strong>
                {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
            </strong>

            al

            <strong>
                {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
            </strong>

        </div>

    </td>

    <td width="25%">

        <table class="border">

            <tr>

                <td class="text-center">

                    <strong>
                        REPORTE
                    </strong>

                </td>

            </tr>

            <tr>

                <td>

                    Generado:

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

$totalProducts = $products->count();

$totalQuantity = $products->sum('quantity');

$totalAmount = $products->sum('total');

@endphp

<div class="section">

<div class="section-title">
    RESUMEN GENERAL
</div>

<table class="resume">

    <tr>

        <td width="33%">

            <strong>
                {{ $totalProducts }}
            </strong>

            <br>

            Productos

        </td>

        <td width="33%">

            <strong>
                {{ number_format($totalQuantity, 2) }}
            </strong>

            <br>

            Unidades vendidas

        </td>

        <td width="34%">

            <strong>
                $ {{ number_format($totalAmount, 2) }}
            </strong>

            <br>

            Importe vendido

        </td>

    </tr>

</table>

</div>

{{-- =====================================================
PRODUCTOS
===================================================== --}}

<div class="section">

<div class="section-title">
    PRODUCTOS VENDIDOS
</div>

<table class="grid">

    <thead>

        <tr>

            <th width="8%">
                #
            </th>

            <th width="42%">
                Producto
            </th>

            <th width="15%">
                Ventas
            </th>

            <th width="15%">
                Cantidad
            </th>

            <th width="20%">
                Total vendido
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($products as $index => $product)

        <tr>

            <td class="text-center">
                {{ $index + 1 }}
            </td>

            <td>
                {{ $product['name'] ?? $product->name }}
            </td>

            <td class="text-center">
                {{ $product['sales_count'] ?? $product->sales_count }}
            </td>

            <td class="text-right">
                {{ number_format($product['quantity'] ?? $product->quantity, 2) }}
            </td>

            <td class="text-right">

                $ {{ number_format(
                    $product['total'] ?? $product->total,
                    2
                ) }}

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="5" class="text-center">

                No existen productos vendidos
                durante el periodo seleccionado.

            </td>

        </tr>

    @endforelse

    </tbody>

    @if($products->count())

    <tfoot>

        <tr>

            <th colspan="2" class="text-right">
                TOTAL
            </th>

            <th class="text-center">
                {{ $products->sum('sales_count') }}
            </th>

            <th class="text-right">
                {{ number_format($totalQuantity, 2) }}
            </th>

            <th class="text-right">
                $ {{ number_format($totalAmount, 2) }}
            </th>

        </tr>

    </tfoot>

    @endif

</table>

</div>

{{-- =====================================================
NOTA
===================================================== --}}

<div class="section">

<table class="border">

    <tr>

        <td>

            <strong>
                Información:
            </strong>

            Los productos se muestran ordenados de mayor a menor
            importe vendido durante el periodo seleccionado.

            Este reporte permite identificar qué productos tienen
            mayor movimiento y cuáles presentan menor cantidad de ventas.

        </td>

    </tr>

</table>

</div>

{{-- =====================================================
PIE
===================================================== --}}

<div class="footer">

Documento generado automáticamente por el ERP SIMONEL.

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
