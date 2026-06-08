@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar elemento al inventario')
@section('tab_title', 'Agregar elemento al inventario | ' . config('app.name'))
@section('description', 'Agregar elemento al inventario.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Agregar producto al inventario
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario/') }}">Ver todos los elementos en el inventario</a>
            </p>

            <inventory-form
                action="{{ url('admin/inventario-clientes/crear') }}"
                :clients='@json($users)'
                :labels='@json($labels)'
                :products='@json($products)'
                >
            </inventory-form>

        </div>
    </section>

@endsection
