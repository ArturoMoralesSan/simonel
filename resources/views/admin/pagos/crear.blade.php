@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar tipos de pagos')
@section('tab_title', 'Agregar tipos de pagos | ' . config('app.name'))
@section('description', 'Agregar tipos de pagos.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar tipo de pago
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/tipos-pagos/') }}">Ver todos los tipos de pago</a>
        </p>

            <base-form action="{{ url('admin/tipos-pagos/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del tipo de pago
                        </h3>

                        <div class="md:row">
                            <div class="md:col-2/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="80" initial=""></text-field>
                                    <field-errors name="name"></field-errors>

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
