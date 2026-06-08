@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar medida')
@section('tab_title', 'Editar medida | ' . config('app.name'))
@section('description', 'Editar medida.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar medida
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/medidas/') }}">Ver todas las medidas</a>
        </p>

            <base-form action="{{ url('admin/medidas/'. $measure->id .'/actualizar') }}"
                method="put"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del estudio
                        </h3>

                        <div class="md:row">
                            <div class="md:col-2/3">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial="{{ $measure->name }}"></text-field>
                                    <field-errors name="name"></field-errors>
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
