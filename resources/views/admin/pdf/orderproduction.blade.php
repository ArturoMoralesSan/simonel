<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Orden de Producción #{{ $order->order_number }}
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

        .section-title {
            background-color: #eeeeee;
            border: 1px solid #000;
            padding: 6px;
            font-weight: bold;
            margin-top: 15px;
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

        {{-- LOGO --}}

        <td width="18%" class="border text-center">

            <img
                style="width: 150px; margin: 20px auto;"
                src="{{ url('img/simonel.png') }}"
                alt=""
            >

        </td>


        {{-- TITULO --}}

        <td width="57%" class="border text-center">

            <div class="title">
                SIMONEL
            </div>

            <div class="subtitle">
                ORDEN DE PRODUCCIÓN
            </div>

            <br>

            <strong>
                #{{ $order->order_number }}
            </strong>

            <br><br>

            Estado:

            <strong>
                {{ $order->status }}
            </strong>

            <br><br>

            Fecha elaboración:

            <strong>
                {{ $order->formated_issue_date }}
            </strong>

        </td>


        {{-- GENERADO --}}

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

    $pesoCombo = (float) ($order->combo_weight ?? 0);

    $totalKg = $order->yields
        ->where('unit', 'kg')
        ->sum('quantity');

    $desjugue = $order->yields
        ->firstWhere('type', 'desjugue');

    $diferencia = $pesoCombo - $totalKg;

    $totalProductos = $order->products->sum('quantity');

    $cantidadProductos = $order->products->count();

@endphp


<table class="summary">

    <tr>

        <td width="25%">

            <strong>
                {{ $cantidadProductos }}
            </strong>

            <br>

            Productos

        </td>


        <td width="25%">

            <strong>
                {{ number_format($totalProductos, 3) }}
            </strong>

            <br>

            Total a producir

        </td>


        <td width="25%">

            <strong>
                {{ number_format($pesoCombo, 3) }}
            </strong>

            <br>

            Peso del combo

        </td>


        <td width="25%">

            <strong>
                $ {{ number_format($order->estimated_cost, 2) }}
            </strong>

            <br>

            Costo estimado

        </td>

    </tr>

</table>


<br>


{{-- =====================================================
    INFORMACIÓN GENERAL
===================================================== --}}

<div class="section-title">

    INFORMACIÓN GENERAL

</div>


<table class="info">

    <tr>

        <td width="20%" class="border">

            <strong>
                ORDEN
            </strong>

        </td>

        <td width="30%" class="border">

            #{{ $order->order_number }}

        </td>


        <td width="20%" class="border">

            <strong>
                ESTADO
            </strong>

        </td>

        <td width="30%" class="border">

            {{ ucfirst($order->status) }}

        </td>

    </tr>


    <tr>

        <td class="border">

            <strong>
                FECHA ELABORACIÓN
            </strong>

        </td>

        <td class="border">

            {{ $order->formated_issue_date }}

        </td>


        <td class="border">

            <strong>
                FECHA ENTREGA
            </strong>

        </td>

        <td class="border">

            {{ $order->formated_delivery_date }}

        </td>

    </tr>


    <tr>

        <td class="border">

            <strong>
                AUTORIZÓ
            </strong>

        </td>

        <td colspan="3" class="border">

            {{ $order->authorizer
                ? $order->authorizer->name . ' ' . $order->authorizer->last_name
                : 'Sin autorizar'
            }}

        </td>

    </tr>


    @if($order->notes)

        <tr>

            <td class="border">

                <strong>
                    COMENTARIOS
                </strong>

            </td>

            <td colspan="3" class="border">

                {{ $order->notes }}

            </td>

        </tr>

    @endif

</table>


<br>


{{-- =====================================================
    DESPIECE
===================================================== --}}

<div class="section-title">

    DESPIECE DEL COMBO

</div>


<table class="summary">

    <tr>

        <td width="25%">

            <strong>
                {{ number_format($pesoCombo, 3) }} kg
            </strong>

            <br>

            Peso del combo

        </td>


        <td width="25%">

            <strong>
                {{ number_format($totalKg, 3) }} kg
            </strong>

            <br>

            Total despiece

        </td>


        <td width="25%">

            <strong>
                {{ number_format($diferencia, 3) }} kg
            </strong>

            <br>

            Diferencia

        </td>


        <td width="25%">

            <strong>
                {{ $desjugue
                    ? number_format($desjugue->quantity, 2)
                    : '0.00'
                }} %
            </strong>

            <br>

            Desjugue

        </td>

    </tr>

</table>


<table class="detail">

    <thead>

        <tr>

            <th width="40%">
                Concepto
            </th>

            <th width="30%" class="text-center">
                Cantidad
            </th>

            <th width="30%" class="text-center">
                % del combo
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($order->yields->where('unit', 'kg') as $yield)

            <tr>

                <td>

                    {{ ucfirst($yield->type) }}

                </td>


                <td class="text-center">

                    {{ number_format($yield->quantity, 3) }}

                    {{ $yield->unit }}

                </td>


                <td class="text-center">

                    {{ $pesoCombo > 0
                        ? number_format(
                            ($yield->quantity / $pesoCombo) * 100,
                            2
                        )
                        : '0.00'
                    }} %

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="3" class="text-center">

                    No existen datos de despiece.

                </td>

            </tr>

        @endforelse

    </tbody>


    <tfoot>

        <tr>

            <th>

                TOTAL

            </th>


            <th class="text-center">

                {{ number_format($totalKg, 3) }}

                kg

            </th>


            <th class="text-center">

                {{ $pesoCombo > 0
                    ? number_format(
                        ($totalKg / $pesoCombo) * 100,
                        2
                    )
                    : '0.00'
                }} %

            </th>

        </tr>

    </tfoot>

</table>


<br>


{{-- =====================================================
    PRODUCTOS
===================================================== --}}

<div class="section-title">

    PRODUCTOS A PRODUCIR

</div>


<table class="detail">

    <thead>

        <tr>

            <th width="40%">
                Producto
            </th>

            <th width="15%" class="text-center">
                Cantidad
            </th>

            <th width="20%" class="text-center">
                Tipo
            </th>

            <th width="25%" class="text-right">
                Costo estimado
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($order->products as $product)

            <tr>

                <td>

                    {{ $product->manufactured->name ?? '-' }}

                    @if($product->manufactured->description ?? false)

                        <br>

                        {{ $product->manufactured->description }}

                    @endif

                </td>


                <td class="text-center">

                    {{ number_format($product->quantity, 3) }}

                </td>


                <td class="text-center">

                    {{ ucfirst(
                        $product->product_type ?? 'producto'
                    ) }}

                </td>


                <td class="text-right">

                    $ {{ number_format(
                        $product->estimated_cost,
                        2
                    ) }}

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    No existen productos.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>


<br>


{{-- =====================================================
    MATERIAS PRIMAS
===================================================== --}}

<div class="section-title">

    MATERIAS PRIMAS REQUERIDAS

</div>


<table class="detail">

    <thead>

        <tr>

            <th width="40%">
                Materia Prima
            </th>

            <th width="20%" class="text-center">
                Cantidad
            </th>

            <th width="15%" class="text-center">
                Unidad
            </th>

            <th width="25%" class="text-right">
                Costo
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($order->items as $item)

            <tr>

                <td>

                    {{ $item->rawMaterial->name ?? '-' }}

                </td>


                <td class="text-center">

                    {{ number_format(
                        $item->quantity,
                        3
                    ) }}

                </td>


                <td class="text-center">

                    {{ $item->unit }}

                </td>


                <td class="text-right">

                    $ {{ number_format(
                        $item->estimated_cost,
                        2
                    ) }}

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    No existen materias primas.

                </td>

            </tr>

        @endforelse

    </tbody>


    <tfoot>

        <tr>

            <th colspan="3" class="text-right">

                COSTO TOTAL

            </th>

            <th class="text-right">

                $ {{ number_format(
                    $order->estimated_cost,
                    2
                ) }}

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

    Orden de producción:

    <strong>
        #{{ $order->order_number }}
    </strong>

    <br>

    Estado:

    <strong>
        {{ $order->status }}
    </strong>

    <br>

    Fecha de elaboración:

    {{ $order->formated_issue_date }}

    <br>

    Fecha de entrega:

    {{ $order->formated_delivery_date }}

    <br>

    Costo estimado:

    <strong>
        $ {{ number_format($order->estimated_cost, 2) }}
    </strong>

    <br>

    Generado el:

    {{ now()->format('d/m/Y H:i:s') }}

</div>


</body>

</html>