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
            padding: 6px;
            vertical-align: middle;
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

        .header-table {
            margin-bottom: 15px;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle;
        }

        .info-cell {
            width: 50%;
            vertical-align: middle;
        }

        .generated-cell {
            width: 30%;
            vertical-align: top;
        }

        .generated-title {
            font-weight: bold;
            background: #efefef;
            text-align: center;
        }

        .period {
            margin-top: 8px;
            font-size: 12px;
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

        .customers {
            margin-top: 10px;
        }

        .customers th {
            background: #efefef;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .customers td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .total-row th {
            background: #efefef;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
            line-height: 14px;
        }

    </style>

</head>

<body>

<div class="page">


    {{-- =====================================================
    VARIABLES
    ====================================================== --}}

    @php

        $totalSales = $totalSales ?? 0;

        $totalAmount = $totalAmount ?? 0;

        $customersCount = $customers->count();

    @endphp



    {{-- =====================================================
    ENCABEZADO
    ====================================================== --}}

    <table class="header-table">

        <tr>


            {{-- LOGO --}}

            <td class="border logo-cell text-center">

                <img
                    src="{{ url('img/simonel.png') }}"
                    style="
                        width:150px;
                        margin:15px auto;
                        display:block;
                    "
                >

            </td>



            {{-- INFORMACIÓN --}}

            <td class="border info-cell text-center">

                <div class="title">

                    SIMONEL

                </div>


                <div class="subtitle">

                    REPORTE DE CLIENTES

                </div>


                <div class="period">

                    Periodo

                    <strong>

                        {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}

                    </strong>

                    al

                    <strong>

                        {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}

                    </strong>

                </div>

            </td>



            {{-- GENERADO --}}

            <td class="generated-cell">

                <table class="border">

                    <tr>

                        <td class="generated-title">

                            Generado

                        </td>

                    </tr>


                    <tr>

                        <td class="text-center">

                            {{ now()->format('d/m/Y H:i') }}

                        </td>

                    </tr>

                </table>


                <table class="resume">

                    <tr>


                        {{-- CLIENTES --}}

                        <td width="50%">

                            <strong>

                                {{ $customersCount }}

                            </strong>

                            <br>

                            Clientes

                        </td>


                        {{-- TOTAL --}}

                        <td width="50%">

                            <strong>

                                $

                                {{ number_format($totalAmount, 2) }}

                            </strong>

                            <br>

                            Total vendido

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>



    {{-- =====================================================
    RESUMEN
    ====================================================== --}}

    <div class="section">

        <div class="section-title">

            RESUMEN DE CLIENTES

        </div>


        <table class="customers">

            <thead>

                <tr>

                    <th width="8%">

                        #

                    </th>

                    <th width="52%">

                        Cliente

                    </th>

                    <th width="20%">

                        Compras

                    </th>

                    <th width="20%">

                        Total comprado

                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($customers as $index => $customer)

                    <tr>

                        {{-- NUMERO --}}

                        <td class="text-center">

                            {{ $index + 1 }}

                        </td>


                        {{-- CLIENTE --}}

                        <td>

                            {{ $customer->name }}

                        </td>


                        {{-- COMPRAS --}}

                        <td class="text-center">

                            {{ $customer->sales_count }}

                        </td>


                        {{-- TOTAL --}}

                        <td class="text-right">

                            $

                            {{ number_format($customer->total, 2) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            class="text-center"
                        >

                            No existen clientes con compras
                            durante el periodo seleccionado.

                        </td>

                    </tr>

                @endforelse

            </tbody>


            {{-- =================================================
            TOTAL
            ================================================== --}}

            <tfoot>

                <tr class="total-row">

                    <th
                        colspan="2"
                        class="text-right"
                    >

                        TOTAL

                    </th>


                    <th class="text-center">

                        {{ $totalSales }}

                    </th>


                    <th class="text-right">

                        $

                        {{ number_format($totalAmount, 2) }}

                    </th>

                </tr>

            </tfoot>

        </table>

    </div>



    {{-- =====================================================
    PIE
    ====================================================== --}}

    <div class="footer">

        Documento generado automáticamente por el ERP SIMONEL.

        <br>

        Reporte general de clientes.

        <br>

        Periodo:

        {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}

        -

        {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}

        <br>

        Generado el

        {{ now()->format('d/m/Y H:i:s') }}

    </div>


</div>

</body>

</html>