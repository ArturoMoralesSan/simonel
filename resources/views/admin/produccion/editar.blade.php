@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar Orden de Producción')
@section('tab_title', 'Editar Orden de Producción | ' . config('app.name'))
@section('description', 'Editar Orden de Producción.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar Orden de Producción
        </h1>
    </div>

    <div class="fluid-container mb-16">

        <p class="mb-12">
            @include('components.alert')

            <span class="color-link">«</span>

            <a href="{{ url('admin/produccion/') }}">
                Ver todas las órdenes de producción
            </a>
        </p>

        <product-order-form 
            action="{{ url('admin/produccion/'. $order->id .'/actualizar') }}"
            method="put"
            :item="10"
            :min-item="1"
            :recipes="{{ $recipes }}"
            :status="{{ $statusLabels }}"
            :order-data="{{ $order }}"
            :assigned-recipes='@json($assignedRecipes)'
        >
        </product-order-form>

    </div>

</section>

@endsection