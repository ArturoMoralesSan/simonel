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

        .sales {
            margin-top: 10px;
        }

        .sales th {
            background: #efefef;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .sales td {
            border: 1px solid #000;
            vertical-align: middle;
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

        /*
        |--------------------------------------------------------------------------
        | Resumen
        |--------------------------------------------------------------------------
        */

        $salesCount = $summary->sales_count ?? 0;

        $total = $summary->total ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Cliente
        |--------------------------------------------------------------------------
        */

        $customerName = optional($customer)->business_name
            ?? 'Público General';


        /*
        |--------------------------------------------------------------------------
        | Acumulados del detalle
        |--------------------------------------------------------------------------
        */

        $detailQuantity = 0;

        $detailTotal = 0;

    @endphp



    {{-- =====================================================
    RESUMEN / ENCABEZADO
    ====================================================== --}}

    <table class="header-table">

        <tr>


            {{-- =================================================
            LOGO
            ================================================== --}}

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



            {{-- =================================================
            INFORMACIÓN
            ================================================== --}}

            <td class="border info-cell text-center">

                <div class="title">

                    SIMONEL

                </div>


                <div class="subtitle">

                    REPORTE DE COMPRAS

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



            {{-- =================================================
            GENERADO / RESUMEN
            ================================================== --}}

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


                        {{-- COMPRAS --}}

                        <td width="50%">

                            <strong>

                                {{ $salesCount }}

                            </strong>

                            <br>

                            Compras

                        </td>



                        {{-- TOTAL --}}

                        <td width="50%">

                            <strong>

                                $

                                {{ number_format($total, 2) }}

                            </strong>

                            <br>

                            Total comprado

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>



    {{-- =====================================================
    DATOS DEL CLIENTE
    ====================================================== --}}

    <div class="section">


        <div class="section-title">

            DATOS DEL CLIENTE

        </div>



        <table style="margin-top:10px;">


            {{-- =================================================
            NOMBRE
            ================================================== --}}

            <tr>

                <td
                    width="20%"
                    class="border"
                >

                    <strong>

                        NOMBRE

                    </strong>

                </td>


                <td
                    width="80%"
                    class="border"
                >

                    {{ $customerName }}

                </td>

            </tr>



            {{-- =================================================
            DIRECCIÓN
            ================================================== --}}

            @if(
                !empty(optional($customer)->street) ||
                !empty(optional($customer)->ext_number)
            )

                <tr>

                    <td class="border">

                        <strong>

                            DIRECCIÓN

                        </strong>

                    </td>


                    <td class="border">

                        {{ optional($customer)->street }}


                        @if(optional($customer)->ext_number)

                            #{{ optional($customer)->ext_number }}

                        @endif


                        @if(optional($customer)->population)

                            , {{ optional($customer)->population }}

                        @endif


                        @if(optional($customer)->postal_code)

                            , C.P.
                            {{ optional($customer)->postal_code }}

                        @endif

                    </td>

                </tr>

            @endif



            {{-- =================================================
            CIUDAD / ESTADO
            ================================================== --}}

            @if(optional($customer)->state)

                <tr>

                    <td class="border">

                        <strong>

                            CIUDAD / ESTADO

                        </strong>

                    </td>


                    <td class="border">

                        {{ optional($customer)->state }}

                    </td>

                </tr>

            @endif

        </table>

    </div>



    {{-- =====================================================
    DETALLE DE COMPRAS
    ====================================================== --}}

    <div class="section">


        <div class="section-title">

            DETALLE DE COMPRAS

        </div>



        <table class="sales">


            <thead>

                <tr>


                    <th width="9%">

                        Fecha

                    </th>


                    <th width="7%">

                        Folio

                    </th>


                    <th width="14%">

                        Vendedor

                    </th>


                    <th width="24%">

                        Producto

                    </th>


                    <th width="9%">

                        Cantidad

                    </th>


                    <th width="11%">

                        Precio

                    </th>


                    <th width="12%">

                        Subtotal

                    </th>


                    <th width="14%">

                        Tipo de pago

                    </th>

                </tr>

            </thead>



            <tbody>


                @forelse($sales as $sale)


                    {{-- =================================================
                    TIPO DE PAGO DE LA VENTA
                    ================================================== --}}

                    @php

                        $payments = $sale->payments ?? collect();


                        $paymentNames = $payments
                            ->map(function ($payment) {

                                return $payment->name
                                    ?? $payment->description
                                    ?? $payment->type
                                    ?? 'No especificado';

                            })
                            ->filter()
                            ->unique()
                            ->implode(', ');


                        if (empty($paymentNames)) {

                            $paymentNames = 'No especificado';

                        }

                    @endphp



                    @foreach($sale->products as $item)


                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Cantidad
                            |--------------------------------------------------------------------------
                            */

                            $quantity = (float) (
                                $item->quantity ?? 0
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Precio de venta
                            |--------------------------------------------------------------------------
                            */

                            $price = (float) (
                                optional($item->product)->costo_venta ?? 0
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Subtotal
                            |--------------------------------------------------------------------------
                            */

                            $subtotal = $quantity * $price;


                            /*
                            |--------------------------------------------------------------------------
                            | Acumulados
                            |--------------------------------------------------------------------------
                            */

                            $detailQuantity += $quantity;

                            $detailTotal += $subtotal;

                        @endphp



                        <tr>


                            {{-- =================================================
                            FECHA
                            ================================================== --}}

                            <td class="text-center">

                                {{ optional($sale->created_at)->format('d/m/Y') }}

                            </td>



                            {{-- =================================================
                            FOLIO
                            ================================================== --}}

                            <td class="text-center">

                                #{{ $sale->id }}

                            </td>



                            {{-- =================================================
                            VENDEDOR
                            ================================================== --}}

                            <td>

                                {{ optional($sale->seller)->name }}

                                {{ optional($sale->seller)->last_name }}

                            </td>



                            {{-- =================================================
                            PRODUCTO
                            ================================================== --}}

                            <td>

                                {{ optional(optional($item->product)->manufactured)->name }}

                            </td>



                            {{-- =================================================
                            CANTIDAD
                            ================================================== --}}

                            <td class="text-center">

                                {{ number_format($quantity, 2) }}

                            </td>



                            {{-- =================================================
                            PRECIO
                            ================================================== --}}

                            <td class="text-right">

                                $

                                {{ number_format($price, 2) }}

                            </td>



                            {{-- =================================================
                            SUBTOTAL
                            ================================================== --}}

                            <td class="text-right">

                                $

                                {{ number_format($subtotal, 2) }}

                            </td>



                            {{-- =================================================
                            TIPO DE PAGO
                            ================================================== --}}

                            <td class="text-center">

                                {{ $paymentNames }}

                            </td>

                        </tr>


                    @endforeach


                @empty


                    <tr>

                        <td
                            colspan="8"
                            class="text-center"
                        >

                            No existen compras para este cliente
                            durante el periodo seleccionado.

                        </td>

                    </tr>


                @endforelse

            </tbody>



            {{-- =================================================
            TOTALES
            ================================================== --}}

            <tfoot>


                {{-- TOTAL DE CANTIDADES --}}

                <tr>

                    <th
                        colspan="4"
                        class="text-right"
                    >

                        TOTAL CANTIDAD

                    </th>


                    <th class="text-center">

                        {{ number_format($detailQuantity, 2) }}

                    </th>


                    <th colspan="3">

                    </th>

                </tr>



                {{-- TOTAL MONETARIO --}}

                <tr>

                    <th
                        colspan="7"
                        class="text-right"
                    >

                        TOTAL

                    </th>


                    <th class="text-right">

                        $

                        {{ number_format($total, 2) }}

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

        Cliente:

        {{ $customerName }}

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