@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar recetas')
@section('tab_title', 'Agregar recetas | ' . config('app.name'))
@section('description', 'Asignar recetas.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Asignar recetas
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/recetas/') }}">Ver todas las recetas</a>
        </p>
        
        <product-recipe-form 
            action="{{ url('admin/recetas/crear') }}"
            :item="4"
            :min-item="1"
            :products="{{ $products }}"
            :materials-data="{{ $rawmaterials }}"
            :assigned-materials="[]"
            :recipes-data="[]"
        >
        </product-recipe-form>

            
            
    </div>
</section>

@endsection
