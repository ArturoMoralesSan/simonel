@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar inventario inicial')
@section('tab_title', 'Agregar inventario inicial | ' . config('app.name'))
@section('description', 'Registrar inventario inicial.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">

    <div class="dashboard-heading">

        <h1 class="dashboard-heading__title">
            Inventario inicial
        </h1>

    </div>

    <div class="fluid-container mb-16">

        <p class="mb-12">

            @include('components.alert')

            <span class="color-link">«</span>

            <a href="{{ url('admin/inventario-inicial') }}">
                Ver inventario inicial
            </a>

        </p>

        <initial-warehouse-form
            action="{{ url('admin/inventario-inicial') }}"
            :item="40"
            :min-item="1"
            :materials="{{ $materials }}"
            :products="{{ $products }}"
            :warehouses="{{ $warehouses }}"
        >
        </initial-warehouse-form>

    </div>

</section>

@endsection