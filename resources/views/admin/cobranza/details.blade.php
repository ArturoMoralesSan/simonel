@extends('layout.dashboard-master')

@section('title', 'Detalle cobranza')
@section('tab_title', 'Detalle cobranza | ' . config('app.name'))

@section('content')

<section class="mb-16">

<div class="dashboard-heading">

    <h1 class="dashboard-heading__title">
        Cuenta por cobrar #{{ $account->id }}
    </h1>

</div>


<div class="fluid-container">

 @include('components.alert')
<p class="mb-8">
    <span class="color-link">«</span>
    <a href="{{ url('admin/cuentas-pendientes') }}">
        Volver a cobranza
    </a>
</p>


{{-- Información general --}}

<section class="db-panel">

<h3 class="db-panel__title">
    Información general
</h3>


<div class="row">

<div class="md:col-1/3">

<strong>Cliente</strong>

<p>
{{ $account->customer->business_name ?? '-' }}
</p>

</div>


<div class="md:col-1/3">

<strong>Venta</strong>

<p>
#{{ $account->sale_id }}
</p>

</div>


<div class="md:col-1/3">

<strong>Vendedor</strong>

<p>
{{ $account->seller->name ?? '-' }}
</p>

</div>

</div>


<hr>


<div class="row mt-4">


<div class="md:col-1/4">

<strong>Fecha venta</strong>

<p>
{{ $account->formated_issue_date }}
</p>

</div>


<div class="md:col-1/4">

<strong>Fecha vencimiento</strong>

<p>
{{ $account->formated_due_date }}
</p>

</div>


<div class="md:col-1/4">

<strong>Total venta</strong>

<p>
${{ number_format($account->original_amount,2) }}
</p>

</div>


<div class="md:col-1/4">

<strong>Saldo pendiente</strong>

<p>
${{ number_format($account->balance,2) }}
</p>

</div>


</div>


</section>


{{-- Estado --}}

<section class="db-panel mt-8">

<h3 class="db-panel__title">
Estado de cuenta
</h3>


<div class="row">


<div class="md:col-1/3">

<strong>Estado</strong>

<p>
{{ $account->status }}
</p>

</div>


<div class="md:col-1/3">

<strong>Pagado</strong>

<p>
${{ number_format($account->paid_amount,2) }}
</p>

</div>


<div class="md:col-1/3">

<strong>Porcentaje pagado</strong>

<p>
{{ $account->percent_paid }}%
</p>

</div>


</div>


</section>



{{-- Pagos --}}

<section class="db-panel mt-8">

<h3 class="db-panel__title">
Pagos realizados
</h3>


@if(!$account->payments->count())

<p class="text-center">
No hay pagos registrados.
</p>


@else

<table class="table">

<thead>

<tr>
<th>Fecha</th>
<th>Método</th>
<th>Monto</th>
</tr>

</thead>


<tbody>

@foreach($account->payments as $payment)

<tr>

    <td>
        {{ $payment->payment_date->format('d/m/Y') }}
    </td>

    <td>
        {{ $payment->payment->name }}
    </td>

    <td>
        ${{ number_format($payment->amount, 2) }}
    </td>

</tr>

@endforeach


</tbody>


</table>

@endif


</section>



{{-- Productos --}}

<section class="db-panel mt-8">

<h3 class="db-panel__title">
Productos vendidos
</h3>


<table class="table">

<thead>

<tr>

<th>
Producto
</th>

<th>
Cantidad
</th>

<th>
Precio
</th>

</tr>

</thead>


<tbody>


@foreach($account->sale->products as $product)
<tr>

<td>
{{ $product->product->manufactured->name ?? '-' }}
</td>


<td>
{{ $product->quantity }}
</td>


<td>
${{ number_format($product->total_with_iva,2) }}
</td>


</tr>


@endforeach


</tbody>


</table>


</section>


</div>

</section>

@endsection