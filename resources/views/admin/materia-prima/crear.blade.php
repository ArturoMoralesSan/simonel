@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar materia prima')
@section('tab_title', 'Agregar materia prima | ' . config('app.name'))
@section('description', 'Agregar materia prima.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar materia prima
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/materia-prima/') }}">Ver todas las materias primas</a>
        </p>

            <base-form action="{{ url('admin/materia-prima/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos de la materia prima
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
                                    <label for="material_types">Tipo de materia</label>
                                    <select-field class="form-select" name="material_types" v-model="fields.material_types"
                                        :options="{{ $material_types }}">
                                    </select-field>
                                                                        
                                    <field-errors name="material_types"></field-errors>
                                </div>
                            </div>

                        </div>
                        <div class="md:row mt-4">
                            <div class="md:col-1/4">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="unit">Unidad</label>
                                    <text-field name="unit" v-model="fields.unit" maxlength="80" initial=""></text-field>
                                    <field-errors name="unit"></field-errors>

                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="stock">Stock mínimo</label>
                                    <text-field name="stock" type="number" v-model="fields.stock" maxlength="5" initial=""></text-field>
                                    <field-errors name="stock"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="cost">Costo unitario <span class="description"> $</span></label>
                                    <text-field name="cost" v-model="fields.cost" maxlength="20" initial=""></text-field>
                                    <field-errors name="cost"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="expiration_days">Días de expiración <span class="description">(opcional)</span></label>
                                    <text-field name="expiration_days" type="number" v-model="fields.expiration_days" maxlength="5" initial=""></text-field>
                                    <field-errors name="expiration_days"></field-errors>
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
