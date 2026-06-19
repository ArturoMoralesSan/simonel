@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar materia prima')
@section('tab_title', 'Editar materia prima | ' . config('app.name'))
@section('description', 'Editar materia prima.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar materia prima
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/materia-prima/') }}">Ver toda las materias primas</a>
        </p>
        

             <base-form action="{{ url('admin/materia-prima/'. $material->id .'/actualizar') }}"
                method="put"
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
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial="{{ $material->name }}"></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="material_types">Tipo de materia</label>
                                    <select-field class="form-select" name="material_types" v-model="fields.material_types"
                                        :options="{{ $material_types }}" initial="{{ $material->raw_material_type }}">
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
                                    <text-field name="unit" v-model="fields.unit" maxlength="80" initial="{{ $material->unit }}"></text-field>
                                    <field-errors name="unit"></field-errors>

                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="stock">Stock mínimo</label>
                                    <text-field name="stock" type="number" v-model="fields.stock" maxlength="5" initial="{{ $material->minimum_stock }}"></text-field>
                                    <field-errors name="stock"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="cost">Costo unitario <span class="description"> $</span></label>
                                    <text-field name="cost" v-model="fields.cost" maxlength="20" initial="{{ $material->cost }}"></text-field>
                                    <field-errors name="cost"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="expiration_days">Días de expiración</label>
                                    <text-field name="expiration_days" type="number" v-model="fields.expiration_days" maxlength="5" initial="{{ $material->expiration_days }}"></text-field>
                                    <field-errors name="expiration_days"></field-errors>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="text-center">
                        <form-button class="btn btn--blue--dashboard btn--wide">
                            Actualizar
                        </form-button>
                    </div>
                </form>
            </base-form>
    </div>
</section>

@endsection
