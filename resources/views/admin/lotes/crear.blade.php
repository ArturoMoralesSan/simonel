@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar lote')
@section('tab_title', 'Agregar lote | ' . config('app.name'))
@section('description', 'Agregar lote.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar lote
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/lotes/') }}">Ver todos los lotes</a>
        </p>

            <base-form action="{{ url('admin/lotes/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del lote
                        </h3>

                        <div class="md:row">
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="raw_material_id">Materia prima</label>
                                    <select-field class="form-select" name="raw_material_id" v-model="fields.raw_material_id"
                                        :options="{{ $raw_materials }}">
                                    </select-field>
                                                                        
                                    <field-errors name="raw_material_id"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="warehouse_id">Almacen</label>
                                    <select-field class="form-select" name="warehouse_id" v-model="fields.warehouse_id"
                                        :options="{{ $warehouses }}">
                                    </select-field>
                                                                        
                                    <field-errors name="warehouse_id"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="lot_number">Número de lote</label>
                                    <text-field name="lot_number" v-model="fields.lot_number" maxlength="80" initial=""></text-field>
                                    <field-errors name="lot_number"></field-errors>
                                </div>
                            </div>

                        </div>
                        <div class="md:row mt-4">
                            <div class="md:col-1/3">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="supplier_lot">Proveedor</label>
                                    <text-field name="supplier_lot" v-model="fields.supplier_lot" maxlength="80" initial=""></text-field>
                                    <field-errors name="supplier_lot"></field-errors>

                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="entry_date">Fecha de entrada</label>
                                    <date-field 
                                        :name="'entry_date'"
                                        v-model="fields.entry_date"
                                    />
                                    <field-errors name="'entry_date'"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="initial_quantity">Cantidad inicial</label>
                                    <text-field name="initial_quantity" v-model="fields.initial_quantity" maxlength="20" initial=""></text-field>
                                    <field-errors name="initial_quantity"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mt-4">
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="available_quantity">Cantidad disponible</label>
                                    <text-field name="available_quantity" v-model="fields.available_quantity" maxlength="20" initial=""></text-field>
                                    <field-errors name="available_quantity"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="cost">Costo <span class="description"> $</span></label>
                                    <text-field name="cost" type="number" v-model="fields.cost" maxlength="5" initial=""></text-field>
                                    <field-errors name="cost"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label for="status">Estado</label>
                                    <select-field class="form-select" name="status" v-model="fields.status"
                                        :options="{{ $status }}">
                                    </select-field>
                                                                        
                                    <field-errors name="status"></field-errors>
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
