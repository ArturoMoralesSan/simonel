@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar presentaciones')
@section('tab_title', 'Agregar presentaciones | ' . config('app.name'))
@section('description', 'Agregar presentaciones.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar presentaciones
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/acabados/') }}">Ver todas las presentaciones</a>
        </p>

            <base-form action="{{ url('admin/acabados/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos de la presentación
                        </h3>

                        <div class="md:row">
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="80" initial=""></text-field>
                                    <field-errors name="name"></field-errors>

                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="measure">Medida o Peso</label>
                                    <text-field name="measure" v-model="fields.measure" maxlength="100" initial=""></text-field>
                                    <field-errors name="measure"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="cost">Costo</label>
                                    <text-field name="cost" v-model="fields.cost" maxlength="80" initial=""></text-field>
                                    <field-errors name="cost"></field-errors>

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
