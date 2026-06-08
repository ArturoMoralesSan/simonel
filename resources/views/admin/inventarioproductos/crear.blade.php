@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar elemento al inventario')
@section('tab_title', 'Agregar elemento al inventario | ' . config('app.name'))
@section('description', 'Agregar elemento al inventario.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Agregar inventario
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario/') }}">Ver todos los elementos en el inventario</a>
            </p>

            <base-form action="{{ url('admin/inventario/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos para el inventario
                        </h3>

                        <div class="md:row mb-2">
                            <div class="md:col">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="product_id">Producto</label>
                                    <select-field class="form-select" name="product_id" v-model="fields.product_id"
                                        :options="{{ $products }}">
                                    </select-field>
                                                                        
                                    <field-errors name="product_id"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-2">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="tag">Etiquetado</label>
                                    <select-field class="form-select" name="tag" v-model="fields.tag"
                                        :options="{{ $tags }}">
                                    </select-field>
                                                                        
                                    <field-errors name="tag"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="type">Tipo</label>
                                    <select-field class="form-select" name="type" v-model="fields.type"
                                        :options="{{ $types }}">
                                    </select-field>
                                                                        
                                    <field-errors name="type"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-2">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="quantity">Cantidad</label>
                                    <text-field name="quantity" v-model="fields.quantity" maxlength="5" initial=""></text-field>
                                    <field-errors name="quantity"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="quantity_min">Cantidad mínima permitida</label>
                                    <text-field name="quantity_min" v-model="fields.quantity_min" maxlength="5" initial=""></text-field>
                                    <field-errors name="quantity_min"></field-errors>
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
