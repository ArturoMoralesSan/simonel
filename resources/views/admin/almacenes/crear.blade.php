@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar almacén')
@section('tab_title', 'Agregar almacén | ' . config('app.name'))
@section('description', 'Agregar almacén.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar almacén
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/almacenes/') }}">Ver todos los almacenes</a>
        </p>

            <base-form action="{{ url('admin/almacenes/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del almacén
                        </h3>

                        <div class="md:row">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial=""></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="warehouse_type">Tipo de almacén</label>
                                    <select-field class="form-select" name="warehouse_type" v-model="fields.warehouse_type"
                                        :options="{{ $warehouse_types }}">
                                    </select-field>
                                                                        
                                    <field-errors name="warehouse_type"></field-errors>
                                </div>
                            </div>
                            

                        </div>
                    </section>
                    <div class="text-center">
                        <form-button class="btn--success btn--wide">
                            Crear
                        </form-button>
                    </div>
                </form>
            </base-form>
    </div>
</section>

@endsection
