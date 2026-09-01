<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de almacén de materias primas
    </title>

    <style>

        @page {
            margin: 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
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
        }

        .section-title {
            margin-top: 18px;
            background: #efefef;
            border: 1px solid #000;
            padding: 6px;
            font-weight: bold;
        }

        .grid {
            margin-top: 10px;
        }

        .grid th {
            border: 1px solid #000;
            background: #efefef;
        }

        .grid td {
            border: 1px solid #000;
        }

        .resume {
            margin-top: 10px;
        }

        .resume td {
            border: 1px solid #000;
            text-align: center;
        }

        .resume strong {
            font-size: 18px;
        }

        .muted {
            color: #6b7280;
            font-size: 10px;
        }

        .total-box {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }

        .total-box span {
            font-size: 16px;
            font-weight: bold;
        }

        .badge {
            padding: 4px 8px;
            font-size: 10px;
            background: #e5e7eb;
            border-radius: 4px;
            display: inline-block;
        }

    </style>

</head>

<body>


{{-- =========================================================
    ENCABEZADO
========================================================= --}}

<table>

    <tr>

        <td
            width="18%"
            class="border text-center"
        >

            <img
                src="{{ url('img/simonel.png') }}"
                style="
                    width:150px;
                    margin:15px auto;
                    display:block;
                "
            >

        </td>


        <td
            width="57%"
            class="border text-center"
        >

            <div class="title">
                SIMONEL
            </div>

            <div class="subtitle">
                REPORTE DE ALMACÉN DE MATERIAS PRIMAS
            </div>

            <br>

            <strong>
                {{ $warehouse->name }}
            </strong>

            <br>

            <span class="small">
                Almacén #{{ $warehouse->id }}
            </span>

            <br><br>

            Periodo

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


{{-- =========================================================
    INFORMACIÓN DEL ALMACÉN
========================================================= --}}

<div class="section-title">

    RESUMEN DEL ALMACÉN

</div>


<table class="resume">

    <tr>

        <td>

            <strong>
                {{ number_format($summary->lots_count, 0) }}
            </strong>

            <br>

            Lotes

        </td>


        <td>

            <strong>
                {{ number_format($summary->materials_count, 0) }}
            </strong>

            <br>

            Materias primas

        </td>


        <td>

            <strong>
                {{ number_format($summary->quantity, 3) }}
            </strong>

            <br>

            Cantidad disponible

        </td>


        <td>

            <strong>
                ${{ number_format($summary->total, 2) }}
            </strong>

            <br>

            Valor del inventario

        </td>

    </tr>

</table>


{{-- =========================================================
    DETALLE
========================================================= --}}

<div class="section-title">

    DETALLE DE MATERIAS PRIMAS

</div>


<table class="grid">

    <thead>

        <tr>

            <th width="25%">
                Materia prima
            </th>

            <th width="18%">
                Lote
            </th>

            <th width="15%">
                Cantidad disponible
            </th>

            <th width="17%">
                Costo unitario
            </th>

            <th width="25%">
                Valor disponible
            </th>

        </tr>

    </thead>


    <tbody>

    @forelse($lots as $lot)

        <tr>

            {{-- MATERIA PRIMA --}}

            <td>

                {{ optional($lot->material)->name
                    ?? 'Materia prima eliminada'
                }}

            </td>


            {{-- LOTE --}}

            <td class="text-center">

                {{ $lot->lot_number ?? '-' }}

            </td>


            {{-- CANTIDAD --}}

            <td class="text-right">

                {{ number_format(
                    (float) $lot->available_quantity,
                    3
                ) }}

            </td>


            {{-- COSTO UNITARIO --}}

            <td class="text-right">

                $

                {{ number_format(
                    (float) $lot->cost,
                    2
                ) }}

            </td>


            {{-- VALOR DISPONIBLE --}}

            <td class="text-right">

                $

                {{ number_format(
                    (float) $lot->available_quantity *
                    (float) $lot->cost,
                    2
                ) }}

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="5"
                class="text-center"
            >

                No existen materias primas disponibles
                en este almacén.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>


{{-- =========================================================
    TOTAL
========================================================= --}}

<div class="total-box">

    VALOR TOTAL DEL ALMACÉN:

    <span>

        $

        {{ number_format(
            $summary->total,
            2
        ) }}

    </span>

</div>


{{-- =========================================================
    OBSERVACIONES
========================================================= --}}

<div class="section-title">

    OBSERVACIONES

</div>


<table class="resume">

    <tr>

        <td width="50%">

            <strong>
                Total de lotes
            </strong>

            <br><br>

            {{ number_format(
                $summary->lots_count,
                0
            ) }}

        </td>


        <td width="50%">

            <strong>
                Materias primas diferentes
            </strong>

            <br><br>

            {{ number_format(
                $summary->materials_count,
                0
            ) }}

        </td>

    </tr>


    <tr>

        <td width="50%">

            <strong>
                Cantidad disponible
            </strong>

            <br><br>

            {{ number_format(
                $summary->quantity,
                3
            ) }}

        </td>


        <td width="50%">

            <strong>
                Valor del inventario
            </strong>

            <br><br>

            $

            {{ number_format(
                $summary->total,
                2
            ) }}

        </td>

    </tr>

</table>


{{-- =========================================================
    FIRMAS
========================================================= --}}

<table style="margin-top:55px;">

    <tr>

        <td
            width="50%"
            class="text-center"
        >

            ______________________________

            <br>

            Responsable de almacén

        </td>


        <td
            width="50%"
            class="text-center"
        >

            ______________________________

            <br>

            Autorizó

        </td>

    </tr>

</table>


{{-- =========================================================
    PIE
========================================================= --}}

<div
    style="
        margin-top:25px;
        text-align:center;
        font-size:9px;
        color:#777;
    "
>

    Documento generado automáticamente
    por el ERP SIMONEL.

    <br>

    {{ now()->format('d/m/Y H:i:s') }}

</div>


</body>

</html>
