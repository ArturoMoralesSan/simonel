@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar cotización')
@section('tab_title', 'Agregar cotización | ' . config('app.name'))
@section('description', 'Agregar cotización.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar venta
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/ventas/') }}">Ver todas las ventas</a>
        </p>

        <add-products-form
            :products="{{ $products }}"
            :users="{{ json_encode($users) }}"
            action="{{ url('admin/ventas/crear') }}"
            
        ></add-products-form>
    
    </div>
</section>

@endsection
