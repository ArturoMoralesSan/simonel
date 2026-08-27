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

        .grid {
            margin-top: 10px;
        }

        .grid th {
            background: #efefef;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .grid td {
            border: 1px solid #000;
            vertical-align: middle;
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
            font-size: 17px;
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
        | Producto
        |--------------------------------------------------------------------------
        */

        $productId = $product['id'] ?? $product->id;

        $productName = $product['name'] ?? $product->name;


        /*
        |--------------------------------------------------------------------------
        | Resumen REAL basado en las ventas filtradas
        |--------------------------------------------------------------------------
        */

        $salesCount = $sales->count();

        $quantity = 0;

        $total = 0;


        /*
        |--------------------------------------------------------------------------
        | Calcular cantidad y total directamente desde el detalle
        |--------------------------------------------------------------------------
        */

        foreach ($sales as $sale) {

            $item = $sale->products
                ->firstWhere('product_id', $productId);

            if ($item) {

                $quantity += (float) ($item->quantity ?? 0);

                $total += (float) ($item->total_with_iva ?? 0);

            }

        }

    @endphp


    {{-- =====================================================
    ENCABEZADO
    ====================================================== --}}

    <table>

        <tr>

            {{-- LOGO --}}

            <td width="18%" class="border text-center">

                <img
                    style="
                        width: 150px;
                        margin: 20px auto;
                    "
                    src="{{ url('img/simonel.png') }}"
                    alt=""
                >

            </td>


            {{-- INFORMACIÓN --}}

            <td width="57%" class="border text-center">

                <div class="title">
                    SIMONEL
                </div>

                <div class="subtitle">
                    REPORTE DE PRODUCTO
                </div>

                <br>

                <strong>
                    {{ $productName }}
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


            {{-- INFORMACIÓN DEL PRODUCTO --}}

            <td width="25%">

                <table class="border">

                    <tr>

                        <td class="text-center">

                            <strong>
                                PRODUCTO
                            </strong>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-center">

                            #{{ $productId }}

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
    ====================================================== --}}

    <div class="section">

        <div class="section-title">

            RESUMEN DEL PRODUCTO

        </div>


        <table class="resume">

            <tr>

                {{-- VENTAS --}}

                <td width="33%">

                    <strong>
                        {{ $salesCount }}
                    </strong>

                    <br>

                    Ventas

                </td>


                {{-- CANTIDAD --}}

                <td width="33%">

                    <strong>
                        {{ number_format($quantity, 2) }}
                    </strong>

                    <br>

                    Cantidad vendida

                </td>


                {{-- TOTAL --}}

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
    DETALLE DE VENTAS
    ====================================================== --}}

    <div class="section">

        <div class="section-title">

            DETALLE DE VENTAS

        </div>


        <table class="grid">

            <thead>

                <tr>

                    <th width="10%">
                        Fecha
                    </th>

                    <th width="8%">
                        Folio
                    </th>

                    <th width="18%">
                        Cliente
                    </th>

                    <th width="15%">
                        Vendedor
                    </th>

                    <th width="9%">
                        Cantidad
                    </th>

                    <th width="12%">
                        Precio
                    </th>

                    <th width="13%">
                        Importe
                    </th>

                    <th width="15%">
                        Tipo de pago
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($sales as $sale)

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | Producto vendido
                    |--------------------------------------------------------------------------
                    */

                    $item = $sale->products
                        ->firstWhere('product_id', $productId);


                    /*
                    |--------------------------------------------------------------------------
                    | Tipo de pago
                    |--------------------------------------------------------------------------
                    |
                    | Sale
                    |   ↓
                    | payment_sale
                    |   ↓
                    | payment
                    |
                    */

                    $paymentType = 'No especificado';

                    if ($sale->payments && $sale->payments->count()) {

                        $payment = $sale->payments->first();

                        $paymentType =
                            $payment->name
                            ?? $payment->type
                            ?? $payment->description
                            ?? 'No especificado';

                    }

                @endphp


                @if($item)

                    <tr>

                        {{-- FECHA --}}

                        <td class="text-center">

                            {{ optional($sale->created_at)->format('d/m/Y') }}

                        </td>


                        {{-- FOLIO --}}

                        <td class="text-center">

                            #{{ $sale->id }}

                        </td>


                        {{-- CLIENTE --}}

                        <td>

                            {{ optional(optional($sale->user)->customer)->business_name
                                ?? optional($sale->customer)->name
                                ?? 'Público General' }}

                        </td>


                        {{-- VENDEDOR --}}

                        <td>

                            {{ optional($sale->seller)->name }}

                            {{ optional($sale->seller)->last_name }}

                        </td>


                        {{-- CANTIDAD --}}

                        <td class="text-right">

                            {{ number_format(
                                (float) ($item->quantity ?? 0),
                                2
                            ) }}

                        </td>


                        {{-- PRECIO --}}

                        <td class="text-right">

                            $

                            {{ number_format(
                                (float) ($item->base_price ?? 0),
                                2
                            ) }}

                        </td>


                        {{-- IMPORTE --}}

                        <td class="text-right">

                            $

                            {{ number_format(
                                (float) ($item->total_with_iva ?? 0),
                                2
                            ) }}

                        </td>


                        {{-- TIPO DE PAGO --}}

                        <td class="text-center">

                            {{ $paymentType }}

                        </td>

                    </tr>

                @endif

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center"
                    >

                        No existen ventas para este producto.

                    </td>

                </tr>

            @endforelse

            </tbody>


            {{-- =================================================
            TOTAL
            ================================================== --}}

            <tfoot>

                <tr>

                    <th
                        colspan="4"
                        class="text-right"
                    >

                        TOTAL

                    </th>


                    {{-- TOTAL CANTIDAD --}}

                    <th class="text-right">

                        {{ number_format($quantity, 2) }}

                    </th>


                    {{-- PRECIO --}}

                    <th></th>


                    {{-- TOTAL IMPORTE --}}

                    <th class="text-right">

                        $

                        {{ number_format($total, 2) }}

                    </th>


                    {{-- PAGO --}}

                    <th></th>

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

        Producto:
        {{ $productName }}

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