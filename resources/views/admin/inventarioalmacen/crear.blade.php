@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Gestionar inventario por almacén')
@section('tab_title', 'Gestionar inventario por almacén | ' . config('app.name'))
@section('description', 'Agregar inventario por almacén.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Gestionar inventario por almacén
            </h1>
        </div>

        <div class="fluid-container mb-16">
            
            <inventory-warehouse
                :warehouse-types="{{ $warehouseTypes }}"
            />

        </div>
    </section>

@endsection
