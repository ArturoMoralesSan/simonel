@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Gestionar inventario de tienda de autoservicio')
@section('tab_title', 'Gestionar inventario de tienda de autoservicio | ' . config('app.name'))
@section('description', 'Gestionar inventario de tienda de autoservicio.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Gestionar inventario de tienda de autoservicio
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario-clientes/') }}">Ver todos los inventarios por tiendas de autoservicio</a>
            </p>

            <inventory-form
                action="{{ url('admin/inventario-clientes/crear') }}"
                :clients='@json($users)'
                >
            </inventory-form>

        </div>
    </section>

@endsection
