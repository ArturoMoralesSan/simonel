@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar compra')
@section('tab_title', 'Editar compra | ' . config('app.name'))
@section('description', 'Editar compra.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar compra
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/compras/') }}">Ver todas las compras</a>
        </p>
        <material-purchase-form 
            action="{{ url('admin/compras/crear') }}"
            :item="4"
            :min-item="1"
            :suppliers="{{ $suppliers }}"
            :warehouses="{{ $warehouses }}"
            :materials-data="{{ $rawmaterials }}"
            :assigned-materials="{{ $purchase->items }}"
            :purchase-data="{{ $purchase }}"
        >
        </material-purchase-form>

            
            
    </div>
</section>

@endsection