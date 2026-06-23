@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar catálogo')
@section('tab_title', 'Editar catálogo | ' . config('app.name'))
@section('description', 'Editar catálogo.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar catálogo
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/catalogo/') }}">Ver todo el catálogo de productos</a>
        </p>

            <base-form action="{{ url('admin/catalogo/'. $catalogo->id .'/actualizar') }}"
                method="put"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del catálogo
                        </h3>

                        <div class="md:row">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial="{{ $catalogo->name }}"></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="desc">Descripción</label>
                                    <text-field name="desc" v-model="fields.desc" maxlength="100" initial="{{ $catalogo->desc }}"></text-field>
                                    <field-errors name="description"></field-errors>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="text-center">
                        <form-button class="btn--blue--dashboard btn--wide">
                            Actualizar
                        </form-button>
                    </div>
                </form>
            </user-form>
    </div>
</section>

@endsection
