<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<style>

@page{
    margin:12mm;
}

body{
    font-family:DejaVu Sans,sans-serif;
    font-size:11px;
    color:#222;
}

table{
    width:100%;
    border-collapse:collapse;
}

td,
th{
    padding:5px;
    vertical-align:top;
}

.border{
    border:1px solid #000;
}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.title{
    font-size:22px;
    font-weight:bold;
}

.subtitle{
    font-size:13px;
    font-weight:bold;
}

.small{
    font-size:10px;
}

.section-title{
    margin-top:18px;
    background:#efefef;
    border:1px solid #000;
    padding:6px;
    font-weight:bold;
}

.grid th{
    border:1px solid #000;
    background:#efefef;
}

.grid td{
    border:1px solid #000;
}

.resume td{
    border:1px solid #000;
    text-align:center;
}

.resume strong{
    font-size:18px;
}

</style>

</head>

<body>

<table>

<tr>

<td width="18%" class="border text-center">

<img
src="{{ url('img/simonel.png') }}"
style="width:150px;margin:15px auto;display:block;"
>

</td>

<td width="57%" class="border text-center">

<div class="title">

SIMONEL

</div>

<div class="subtitle">

REPORTE POR VENDEDOR

</div>

<br>

<strong>

{{ $seller->name }}

</strong>

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

<strong>Generado</strong>

</td>

</tr>

<tr>

<td>

{{ now()->format('d/m/Y H:i') }}

</td>

</tr>

</table>

</td>

</tr>

</table>

<div class="section-title">

RESUMEN DEL VENDEDOR

</div>

<table class="resume">

<tr>

<td>

<strong>

{{ $summary->sales_count }}

</strong>

<br>

Ventas

</td>

<td>

<strong>

${{ number_format($summary->total_sales,2) }}

</strong>

<br>

Total vendido

</td>

<td>

<strong>

${{ number_format($summary->cash_total,2) }}

</strong>

<br>

Debe entregar en efectivo

</td>

</tr>

</table>
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

            <th width="20%">
                Cliente
            </th>

            <th width="27%">
                Productos
            </th>

            <th width="20%">
                Métodos de pago
            </th>

            <th width="15%">
                Total
            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($sales as $sale)

        <tr>

            <td class="text-center">

                {{ $sale->created_at->format('d/m/Y') }}

            </td>

            <td class="text-center">

                #{{ $sale->id }}

            </td>

            <td>

                {{ optional($sale->user->customer)->business_name ?? optional($sale->customer)->name }}

            </td>

            <td>

                @foreach($sale->products as $item)

                    <div>

                        {{ optional(optional($item->product)->manufactured)->name }}

                        <strong>

                            ({{ number_format($item->quantity,2) }})

                        </strong>

                    </div>

                @endforeach

            </td>

            <td>

                @foreach($sale->payments as $payment)

                    <div>

                        {{ $payment->name }}

                        -

                        $

                        {{ number_format($payment->pivot->cost,2) }}

                    </div>

                @endforeach

            </td>

            <td class="text-right">

                $

                {{ number_format($sale->total_with_iva,2) }}

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="6" class="text-center">

                No existen ventas para este periodo.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>
<div class="section-title">

OBSERVACIONES

</div>

<table class="resume">

<tr>

<td width="50%">

<strong>

Total de ventas realizadas

</strong>

<br><br>

{{ $summary->sales_count }}

</td>

<td width="50%">

<strong>

Efectivo a entregar

</strong>

<br><br>

$

{{ number_format($summary->cash_total,2) }}

</td>

</tr>

</table>
<table style="margin-top:55px;">

<tr>

<td width="50%" class="text-center">

______________________________

<br>

Firma del vendedor

</td>

<td width="50%" class="text-center">

______________________________

<br>

Autorizó

</td>

</tr>

</table>

<div style="margin-top:25px;text-align:center;font-size:9px;color:#777;">

Documento generado automáticamente por el ERP SIMONEL.

<br>

{{ now()->format('d/m/Y H:i:s') }}

</div>

</body>

</html>