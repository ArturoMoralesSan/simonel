```blade
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

REPORTE DE ALMACENES DE PRODUCTO TERMINADO

</div>

<br>

<strong>

INVENTARIO ACTUAL DISPONIBLE

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
     RESUMEN
========================================================= --}}

<div class="section-title">

RESUMEN DE INVENTARIO

</div>


<table class="resume">

<tr>

<td>

<strong>

{{ number_format($warehouses->count(), 0) }}

</strong>

<br>

Almacenes

</td>


<td>

<strong>

{{ number_format($warehouses->sum('lots_count'), 0) }}

</strong>

<br>

Lotes

</td>


<td>

<strong>

{{ number_format($warehouses->sum('products_count'), 0) }}

</strong>

<br>

Productos

</td>


<td>

<strong>

{{ number_format($warehouses->sum('quantity'), 2) }}

</strong>

<br>

Cantidad disponible

</td>


<td>

<strong>

${{ number_format($warehouses->sum('total'), 2) }}

</strong>

<br>

Valor del inventario

</td>

</tr>

</table>



{{-- =========================================================
     DETALLE DE ALMACENES
========================================================= --}}

<div class="section-title">

DETALLE DE ALMACENES

</div>


<table class="grid">

<thead>

<tr>

<th width="30%">

Almacén

</th>

<th width="15%">

Lotes

</th>

<th width="20%">

Productos

</th>

<th width="15%">

Cantidad

</th>

<th width="20%">

Valor

</th>

</tr>

</thead>


<tbody>

@forelse($warehouses as $warehouse)

<tr>

<td>

{{ $warehouse->name }}

</td>


<td class="text-center">

{{ number_format($warehouse->lots_count, 0) }}

</td>


<td class="text-center">

{{ number_format($warehouse->products_count, 0) }}

</td>


<td class="text-right">

{{ number_format($warehouse->quantity, 2) }}

</td>


<td class="text-right">

$

{{ number_format($warehouse->total, 2) }}

</td>

</tr>

@empty

<tr>

<td colspan="5" class="text-center">

No existen productos disponibles.

</td>

</tr>

@endforelse

</tbody>

</table>



{{-- =========================================================
     TOTAL
========================================================= --}}

<div class="section-title">

TOTAL DEL INVENTARIO

</div>


<table class="resume">

<tr>

<td width="50%">

<strong>

{{ number_format(
    $warehouses->sum('quantity'),
    2
) }}

</strong>

<br>

Cantidad disponible

</td>


<td width="50%">

<strong>

$

{{ number_format(
    $warehouses->sum('total'),
    2
) }}

</strong>

<br>

Valor total del inventario

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
```
