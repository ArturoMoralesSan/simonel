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


{{-- =========================================================
     ENCABEZADO
========================================================= --}}

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

REPORTE DE ALMACÉN DE PRODUCTO TERMINADO

</div>

<br>

<strong>

{{ $warehouse->name }}

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

INFORMACIÓN DEL ALMACÉN

</div>


<table class="resume">

<tr>

<td width="25%">

<strong>

#{{ $warehouse->id }}

</strong>

<br>

ID almacén

</td>


<td width="25%">

<strong>

{{ number_format($summary->lots_count, 0) }}

</strong>

<br>

Lotes

</td>


<td width="25%">

<strong>

{{ number_format($summary->products_count, 0) }}

</strong>

<br>

Productos

</td>


<td width="25%">

<strong>

{{ number_format($summary->quantity, 2) }}

</strong>

<br>

Cantidad disponible

</td>

</tr>

</table>



{{-- =========================================================
     DETALLE DE LOTES
========================================================= --}}

<div class="section-title">

DETALLE DE PRODUCTOS Y LOTES

</div>


<table class="grid">

<thead>

<tr>

<th width="22%">

Producto

</th>

<th width="15%">

Lote

</th>

<th width="13%">

Fecha entrada

</th>

<th width="13%">

Caducidad

</th>

<th width="17%">

Cantidad disponible

</th>

<th width="20%">

Costo total lote

</th>

</tr>

</thead>


<tbody>

@forelse($lots as $lot)

<tr>

<td>

{{ optional($lot->product->manufactured)->name ?? 'Producto eliminado' }}

</td>


<td class="text-center">

{{ $lot->lot_number ?? '-' }}

</td>


<td class="text-center">

{{ $lot->production_date
    ? \Carbon\Carbon::parse($lot->production_date)->format('d/m/Y')
    : '-'
}}

</td>


<td class="text-center">
{{ $lot->expiration_date
    ? \Carbon\Carbon::parse($lot->expiration_date)->format('d/m/Y')
    : '-'
}}

</td>


<td class="text-right">

{{ number_format($lot->available_quantity, 2) }}

</td>


<td class="text-right">

$

{{ number_format($lot->total_cost, 2) }}

</td>

</tr>

@empty

<tr>

<td colspan="6" class="text-center">

No existen productos disponibles en este almacén.

</td>

</tr>

@endforelse

</tbody>

</table>



{{-- =========================================================
     RESUMEN FINAL
========================================================= --}}

<div class="section-title">

RESUMEN DEL INVENTARIO

</div>


<table class="resume">

<tr>

<td width="50%">

<strong>

{{ number_format($summary->quantity, 2) }}

</strong>

<br>

Cantidad total disponible

</td>


<td width="50%">

<strong>

$

{{ number_format($summary->total, 2) }}

</strong>

<br>

Valor total del inventario

</td>

</tr>

</table>



{{-- =========================================================
     OBSERVACIONES
========================================================= --}}

<div class="section-title">

OBSERVACIONES

</div>


<table class="resume">

<tr>

<td>

<strong>

Almacén

</strong>

<br><br>

{{ $warehouse->name }}

</td>


<td>

<strong>

Lotes disponibles

</strong>

<br><br>

{{ number_format($summary->lots_count, 0) }}

</td>


<td>

<strong>

Productos disponibles

</strong>

<br><br>

{{ number_format($summary->products_count, 0) }}

</td>

</tr>

</table>



{{-- =========================================================
     FIRMAS
========================================================= --}}

<table style="margin-top:55px;">

<tr>

<td width="50%" class="text-center">

______________________________

<br>

Responsable de almacén

</td>


<td width="50%" class="text-center">

______________________________

<br>

Autorizó

</td>

</tr>

</table>



{{-- =========================================================
     FOOTER
========================================================= --}}

<div
    style="
        margin-top:25px;
        text-align:center;
        font-size:9px;
        color:#777;
    "
>

Documento generado automáticamente por el ERP SIMONEL.

<br>

{{ now()->format('d/m/Y H:i:s') }}

</div>


</body>

</html>
