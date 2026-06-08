@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar tipo de gasto')
@section('tab_title', 'Editar tipo de gasto | ' . config('app.name'))
@section('description', 'Editar tipo de gasto.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar tipo de pago
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/tipos-gastos/') }}">Ver todos los tipos de gastos</a>
        </p>

            <base-form action="{{ url('admin/tipos-gastos/'. $type_expense->id .'/actualizar') }}"
                method="put"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del tipo de gasto
                        </h3>

                        <div class="md:row">
                            <div class="md:col-2/3">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial="{{ $type_expense->name }}"></text-field>
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
