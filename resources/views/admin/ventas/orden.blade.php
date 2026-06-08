@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Orden de')
@section('tab_title', 'Orden de | ' . config('app.name'))
@section('description', 'Orden de.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Orden de trabajo
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/ventas/') }}">Ver todas las ventas</a>
        </p>

        <section class="db-panel">
            <h3 class="db-panel__title">
                Datos de la orden
            </h3>

            <div class="md:row mb-2">
                <div class="md:col">
                    <table class="order-table__details">
                        <tbody>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">ID de Venta:</th>
                                <td class="order-table__cell">{{ $sale->id }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Cliente:</th>
                                <td class="order-table__cell">{{ $sale->user->name }} {{ $sale->user->last_name }}</td>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Fecha de Creación:</th>
                                <td class="order-table__cell">{{ $sale->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Estado:</th>
                                <td class="order-table__cell">
                                    <span class="badge badge-status badge-{{ $sale->status }}">
                                        {{ $status[$sale->status] ?? ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Subtotal:</th>
                                <td class="order-table__cell">$ {{ number_format($sale->total_sale_price, 4) }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">IVA:</th>
                                <td class="order-table__cell">$ {{ number_format($sale->iva, 4) }}</td>
                            </tr>
                            <tr class="order-table__row">
                                <th class="order-table__cell--heading">Total:</th>
                                <td class="order-table__cell">$ {{ number_format($sale->total_with_iva, 4) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </section>

        
        <div class="md:row mb-2">
            @foreach($sale->templates as $template)
                <div class="md:col-auto">
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            {{ $template->product_name }}
                        </h3>

                        <div class="order-table">
                            <div class="order-table__body">
                                <table class="order-table__details">
                                    <tbody>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Categoría:</th>
                                            <td class="order-table__cell">
                                                {{ $template->product?->type?->name ?? 'Sin Categoría' }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">producto:</th>
                                            <td class="order-table__cell">
                                                {{ $template->pivot->product_name }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Acabado:</th>
                                            <td class="order-table__cell">
                                                {{ $template->cut_name }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Ancho:</th>
                                            <td class="order-table__cell">
                                                {{ $template->width }} cm
                                                
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Largo:</th>
                                            <td class="order-table__cell">
                                                {{ $template->height }} cm
                                                
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Cantidad:</th>
                                            <td class="order-table__cell">
                                                {{ $template->pivot->quantity }}
                                                
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Precio base:</th>
                                            <td class="order-table__cell">
                                                $ {{ number_format($template->pivot->base_price, 4) }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Descuento:</th>
                                            <td class="order-table__cell">
                                                $ {{ number_format($template->pivot->discount, 4) }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">IVA:</th>
                                            <td class="order-table__cell">
                                                $ {{ number_format($template->pivot->iva, 4) }}
                                            </td>
                                        </tr>
                                        <tr class="order-table__row">
                                            <th class="order-table__cell--heading">Importe:</th>
                                            <td class="order-table__cell">
                                                $ {{ number_format($template->pivot->base_price * $template->pivot->quantity , 4) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>

            @endforeach
            
            
            
        </div>

        @if($sale->status != 'paid')
            <order-status-form 
                action="{{ url('admin/ventas/orden/'. $sale->id .'/actualizar') }}"
                method="PUT"
                :status="{{ $status }}"
                :sale-data="{{ $sale }}"
                :payment="3"
                :min-payment="0"
                :payments-data="{{ $payments }}"
                :assigned-payments="{{ $assigned_payments }}"
            >
            </order-status-form>
        @endif
    </div>
</section>

@endsection
