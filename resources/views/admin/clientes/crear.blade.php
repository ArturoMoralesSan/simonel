@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar usuarios')
@section('tab_title', 'Agregar usuarios | ' . config('app.name'))
@section('description', 'Agregar usuarios.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Agregar cliente
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/clientes/') }}">Ver todos los clientes</a>
            </p>

            <customer-form
                action="{{ url('admin/clientes/crear') }}"
                :user="{}"
            >
            </customer-form>
        </div>
    </section>

@endsection
