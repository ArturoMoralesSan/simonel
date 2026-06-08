@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Calculadora')
@section('tab_title', 'Calculadora | ' . config('app.name'))
@section('description', 'Lista de calculadora.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Calculadora de etiquetas + representación gráfica
        </h1>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <tag-calculator
            :cuts="{{ $cuts }}"
            :types="{{ $types }}"
            :products="{{ $products }}"
        ></tag-calculator>
    </div>
@endsection
