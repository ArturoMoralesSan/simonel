@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar recetas')
@section('tab_title', 'Editar recetas | ' . config('app.name'))
@section('description', 'Editar recetas.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar receta
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
            :products='@json($products)'
            :materials-data='@json($rawmaterials)'
            :assigned-materials='@json($recipe->items)'
            :recipes-data='@json($recipe)'
        >
        </product-recipe-form>
            
            
    </div>
</section>

@endsection
