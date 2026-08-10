blade
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de métodos de pago
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
                REPORTE DE MÉTODOS DE PAGO
            </div>

            <br>

            <strong>
                Todos los métodos de pago
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

    $totalOperations = $payments->sum('operations');

    $total = $payments->sum('total');

@endphp


<table class="summary">

    <tr>

        <td width="50%">

            <strong>
                {{ $totalOperations }}
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
    RESUMEN DE MÉTODOS
===================================================== --}}

<table>

    <tr>

        <td width="20%" class="border">

            <strong>
                MÉTODOS
            </strong>

        </td>

        <td width="80%" class="border">

            {{ $payments->count() }}

        </td>

    </tr>


    <tr>

        <td class="border">

            <strong>
                OPERACIONES
            </strong>

        </td>

        <td class="border">

            {{ $totalOperations }}

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

            <th width="10%">
                #
            </th>

            <th width="45%">
                Método de pago
            </th>

            <th width="20%">
                Operaciones
            </th>

            <th width="25%">
                Total
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($payments as $payment)

            <tr>

                <td class="text-center">

                    {{ $loop->iteration }}

                </td>


                <td>

                    <strong>
                        {{ $payment['name'] }}
                    </strong>

                </td>


                <td class="text-center">

                    {{ $payment['operations'] }}

                </td>


                <td class="text-right">

                    $ {{ number_format($payment['total'], 2) }}

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    No existen operaciones de pago
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

                {{ $totalOperations }}

            </th>


            <th class="text-right">

                $ {{ number_format($total, 2) }}

            </th>

        </tr>

    </tfoot>

</table>


<br>


{{-- =====================================================
    PIE
===================================================== --}}

<div class="footer">

    Documento generado automáticamente por el ERP SIMONEL.

    <br><br>

    Reporte:

    <strong>
        Todos los métodos de pago
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
