@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Gestionar inventario de vendedores')
@section('tab_title', 'Gestionar inventario de vendedores | ' . config('app.name'))
@section('description', 'Gestionar inventario de vendedores.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Gestionar inventario de vendedores
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario-vendedores/') }}">Ver todos los inventarios de vendedores</a>
            </p>

            <inventory-seller-form
                action="{{ url('admin/inventario-vendedores/crear') }}"
                :sellers="{{ json_encode($sellers) }}"
                :product-lot-options="{{ json_encode($productLotOptions) }}"
            />

        </div>
    </section>

@endsection