blade
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de método de pago
    </title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 13px;
            font-weight: bold;
        }

        th {
            background-color: #eeeeee;
            border: 1px solid #000;
            padding: 6px;
        }

        td {
            padding: 5px;
            vertical-align: middle;
        }

        .summary td {
            text-align: center;
            border: 1px solid #000;
            padding: 8px;
        }

        .summary strong {
            font-size: 14px;
        }

        .info td {
            padding: 6px;
        }

        .detail td {
            border: 1px solid #000;
        }

        .footer {
            margin-top: 25px;
            font-size: 9px;
            text-align: center;
        }

    </style>

</head>

<body>


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
                REPORTE DE MÉTODO DE PAGO
            </div>

            <br>

            <strong>
                {{ $payment->name }}
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
                            GENERADO
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


<br>


{{-- =====================================================
    RESUMEN
===================================================== --}}

@php

    $operations = $summary->operations ?? 0;

    $total = $summary->total ?? 0;

@endphp


<table class="summary">

    <tr>

        <td width="50%">

            <strong>
                {{ $operations }}
            </strong>

            <br>

            Operaciones

        </td>


        <td width="50%">

            <strong>
                $ {{ number_format($total, 2) }}
            </strong>

            <br>

            Total recibido

        </td>

    </tr>

</table>


<br>


{{-- =====================================================
    DATOS DEL MÉTODO DE PAGO
===================================================== --}}

<table class="info">

    <tr>

        <td width="20%" class="border">

            <strong>
                MÉTODO DE PAGO
            </strong>

        </td>

        <td width="80%" class="border">

            {{ $payment->name }}

        </td>

    </tr>


    <tr>

        <td class="border">

            <strong>
                OPERACIONES
            </strong>

        </td>

        <td class="border">

            {{ $operations }}

        </td>

    </tr>


    <tr>

        <td class="border">

            <strong>
                TOTAL
            </strong>

        </td>

        <td class="border">

            $ {{ number_format($total, 2) }}

        </td>

    </tr>

</table>


<br>


{{-- =====================================================
    DETALLE
===================================================== --}}

<table class="detail">

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

        @forelse($sales as $item)

            @php

                $sale = $item->sale;

                $amount = $item->amount;

            @endphp


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

                    @foreach($sale->products as $productItem)

                        {{ optional(optional($productItem->product)->manufactured)->name }}

                        &nbsp;

                        ({{ number_format($productItem->quantity, 2) }})

                        @if(!$loop->last)

                            <br>

                        @endif

                    @endforeach

                </td>


                <td>

                    {{ $payment->name }}

                    <br>

                    ${{ number_format($amount, 2) }}

                </td>


                <td class="text-right">

                    $ {{ number_format($amount, 2) }}

                </td>

            </tr>


        @empty

            <tr>

                <td colspan="6" class="text-center">

                    No existen operaciones para este método de pago
                    durante el periodo seleccionado.

                </td>

            </tr>

        @endforelse

    </tbody>


    <tfoot>

        <tr>

            <th colspan="5" class="text-right">

                TOTAL {{ strtoupper($payment->name) }}

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

    <br><br>

    Método de pago:

    <strong>
        {{ $payment->name }}
    </strong>

    <br>

    Periodo:

    {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}

    -

    {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}

    <br>

    Total:

    <strong>
        $ {{ number_format($total, 2) }}
    </strong>

    <br>

    Generado el:

    {{ now()->format('d/m/Y H:i:s') }}

</div>


</body>

</html>
