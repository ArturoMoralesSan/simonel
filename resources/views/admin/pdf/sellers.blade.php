<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<style>

    @page {
        margin: 12mm;
        size: landscape;
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
        font-weight: bold;
    }

    .grid td {
        border: 1px solid #000;
    }

    .resume {
        width: 100%;
        border-collapse: collapse;
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

{{-- =========================================================
ENCABEZADO
========================================================= --}}

<table class="header">

<tr>

    <td width="18%" class="border text-center">

        <img
            style="width: 150px; display: block; margin: 20px auto;"
            src="{{ url('img/simonel.png') }}"
            alt=""
        >

    </td>

    <td width="57%" class="text-center border">

        <div class="title">
            SIMONEL
        </div>

        <div class="subtitle">
            PROFR. SIMON MOLINA MALDONADO
        </div>

        <br>

        <strong>
            REPORTE DE VENDEDORES
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

        <table style="width:100%; margin-top:8px;">

            <tr>

                <td
                    class="small"
                    style="width:50%; text-align:left;"
                >
                    Calle Río Tamazula 210
                </td>

                <td
                    class="small"
                    style="width:50%; text-align:left;"
                >
                    Col. Gustavo Díaz Ordaz
                </td>

            </tr>

            <tr>

                <td
                    class="small"
                    style="width:50%; text-align:left;"
                >
                    TEL. (618) 143-19-84
                </td>

                <td
                    class="small"
                    style="width:50%; text-align:left;"
                >
                    CEL. (618) 171-95-12
                </td>

            </tr>

        </table>

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

                    Fecha de generación

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

{{-- =========================================================
RESUMEN GENERAL
========================================================= --}}

@php

$totalVendedores = $sellers->count();

$totalVentas = $sellers->sum('sales_count');

$totalVendido = $sellers->sum('total_sales');

$totalEfectivo = $sellers->sum('cash_total');

@endphp

<div class="section">

<div class="section-title">

    RESUMEN GENERAL

</div>

<table class="resume">

    <tr>

        <td width="25%">

            <strong>
                {{ $totalVendedores }}
            </strong>

            <br>

            Vendedores

        </td>

        <td width="25%">

            <strong>
                {{ $totalVentas }}
            </strong>

            <br>

            Ventas

        </td>

        <td width="25%">

            <strong>
                $ {{ number_format($totalVendido, 2) }}
            </strong>

            <br>

            Total vendido

        </td>

        <td width="25%">

            <strong>
                $ {{ number_format($totalEfectivo, 2) }}
            </strong>

            <br>

            Efectivo a entregar

        </td>

    </tr>

</table>

</div>

{{-- =========================================================
DETALLE DE VENDEDORES
========================================================= --}}

<div class="section">

<div class="section-title">

    DETALLE POR VENDEDOR

</div>

<table class="grid">

    <thead>

        <tr>

            <th width="8%">
                #
            </th>

            <th width="37%">
                Vendedor
            </th>

            <th width="15%">
                Cantidad de ventas
            </th>

            <th width="20%">
                Total vendido
            </th>

            <th width="20%">
                Efectivo a entregar
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($sellers as $index => $seller)

        <tr>

            <td class="text-center">

                {{ $index + 1 }}

            </td>

            <td>

                <strong>
                    {{ $seller->name }}
                </strong>

            </td>

            <td class="text-center">

                {{ $seller->sales_count }}

            </td>

            <td class="text-right">

                $ {{ number_format($seller->total_sales, 2) }}

            </td>

            <td class="text-right">

                <strong>
                    $ {{ number_format($seller->cash_total, 2) }}
                </strong>

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="5"
                class="text-center"
            >

                No existen ventas registradas
                para el periodo seleccionado.

            </td>

        </tr>

    @endforelse

    </tbody>

    @if($sellers->count())

    <tfoot>

        <tr>

            <th
                colspan="2"
                class="text-right"
            >

                TOTAL

            </th>

            <th class="text-center">

                {{ $totalVentas }}

            </th>

            <th class="text-right">

                $ {{ number_format($totalVendido, 2) }}

            </th>

            <th class="text-right">

                $ {{ number_format($totalEfectivo, 2) }}

            </th>

        </tr>

    </tfoot>

    @endif

</table>

</div>

{{-- =========================================================
INFORMACIÓN SOBRE EL EFECTIVO
========================================================= --}}

<div class="section">

<table class="border">

    <tr>

        <td>

            <strong>
                Efectivo a entregar:
            </strong>

            El importe indicado corresponde únicamente a los
            pagos registrados mediante el método de pago
            <strong>efectivo</strong> durante el periodo seleccionado.

            <br><br>

            El total vendido puede incluir otros métodos de pago,
            como transferencia, tarjeta u otros medios registrados
            en el sistema.

        </td>

    </tr>

</table>

</div>

<table style="margin-top: 60px;">
<tr>

    <td width="50%" class="text-center">

        ______________________________

        <br><br>

        <strong>
            RESPONSABLE
        </strong>

    </td>

    <td width="50%" class="text-center">

        ______________________________

        <br><br>

        <strong>
            AUTORIZÓ
        </strong>

    </td>

</tr>
</table>

{{-- =========================================================
PIE
========================================================= --}}

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
