@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Gestionar inventario')
@section('tab_title', 'Gestionar inventario | ' . config('app.name'))
@section('description', 'Gestionar inventario.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Gestionar inventario
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario/') }}">Ver todos los elementos en el inventario</a>
            </p>

            <inventory-product-form
                action="{{ url('admin/inventario/' . $inventory->id . '/actualizar') }}"
                :inventory='@json($inventory)'
                :inputmovements='@json($movementsEntradas)'
                :outputmovements='@json($movementsSalidas)'
                >
            </inventory-product-form>

        </div>
    </section>

@endsection
