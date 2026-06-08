@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar Orden de Producción')
@section('tab_title', 'Agregar Orden de Producción | ' . config('app.name'))
@section('description', 'Agregar Orden de Producción.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar Orden de Producción
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
            action="{{ url('admin/produccion/crear') }}"
            :item="10"
            :min-item="1"
            :recipes="{{ $recipes }}"
            :status="{{ $statusLabels }}"
            :order-data="[]"
            :assigned-recipes="[]"
        >
        </product-order-form>

    </div>

</section>

@endsection