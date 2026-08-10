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

    .header {
        margin-bottom: 15px;
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

    .customers {
        margin-top: 15px;
    }

    .customers th {
        background: #efefef;
        border: 1px solid #000;
    }

    .customers td {
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

<table class="header">

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
            REPORTE DE CLIENTES
        </div>

        <br>

        <strong>
            RESUMEN DE COMPRAS
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

                    <strong>
                        Generado
                    </strong>

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

$totalCustomers = $customers->count();

$totalSales = $customers->sum('sales_count');

$total = $customers->sum('total');

@endphp

<table class="resume">

<tr>

    <td width="33%">

        <strong>
            {{ $totalCustomers }}
        </strong>

        <br>

        Clientes

    </td>

    <td width="33%">

        <strong>
            {{ $totalSales }}
        </strong>

        <br>

        Compras

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

{{-- =====================================================
DETALLE
===================================================== --}}

<table class="customers">

<thead>

    <tr>

        <th width="8%">
            #
        </th>

        <th width="47%">
            Cliente
        </th>

        <th width="15%">
            Compras
        </th>

        <th width="30%">
            Total comprado
        </th>

    </tr>

</thead>

<tbody>

@forelse($customers as $index => $customer)

    <tr>

        <td class="text-center">

            {{ $index + 1 }}

        </td>

        <td>

            {{ $customer->name }}

        </td>

        <td class="text-center">

            {{ $customer->sales_count }}

        </td>

        <td class="text-right">

            $ {{ number_format($customer->total, 2) }}

        </td>

    </tr>

@empty

    <tr>

        <td colspan="4" class="text-center">

            No existen clientes con compras
            durante el periodo seleccionado.

        </td>

    </tr>

@endforelse

</tbody>

<tfoot>

    <tr>

        <th colspan="2" class="text-right">

            TOTAL

        </th>

        <th class="text-center">

            {{ $totalSales }}

        </th>

        <th class="text-right">

            $ {{ number_format($total, 2) }}

        </th>

    </tr>

</tfoot>

</table>

{{-- =====================================================
PIE
===================================================== --}}

<div class="footer">

Documento generado automáticamente por el ERP SIMONEL.

<br>

Reporte de clientes

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
