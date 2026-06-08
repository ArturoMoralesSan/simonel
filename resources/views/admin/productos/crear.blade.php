@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar producto')
@section('tab_title', 'Agregar producto | ' . config('app.name'))
@section('description', 'Agregar producto.')
@section('css_classes', 'dashboard')
@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar producto
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/productos/') }}">Ver todos los productos</a>
        </p>

        <script>
            window.presentations = @json($presentations);
        </script>

            <product-form action="{{ url('admin/productos/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del producto
                        </h3>

                        <div class="md:row mb-2">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="80" initial=""></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="type">Categoría</label>
                                    <select-field class="form-select" name="type_id" v-model="fields.type_id"
                                        :options="{{ $types }}">
                                    </select-field>                                    
                                    <field-errors name="type"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-2">
                            <div class="md:col">
                            {{-- nombres --}}
                                <div class="form-control">
                                    <label for="description">Descripción</label>
                                    <text-field name="description" v-model="fields.description" maxlength="80" initial=""></text-field>
                                    <field-errors name="description"></field-errors>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Costos del producto
                        </h3>

                        <div class="md:row mb-2">
                        <div class="md:col-1/4">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="type">Presentación</label>
                                    <select-field class="form-select" name="cut_id" v-model="fields.cut_id"
                                        :options="{{ $presentationsOptions }}">
                                    </select-field>                                    
                                    <field-errors name="cut_id"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="vinil_cost">Costo de fabricación<span class="description">$</span></label>
                                    <text-field name="vinil_cost" v-model="fields.vinil_cost" maxlength="80"></text-field>
                                    <field-errors name="vinil_cost"></field-errors>
                                </div>
                            </div>

                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="impresion_cost">Costo de presentacion <span class="description">$</span></label>
                                    <text-field disabled name="impresion_cost" v-model="fields.impresion_cost" maxlength="80" ></text-field>
                                    <field-errors name="impresion_cost"></field-errors>
                                </div>
                            </div>

                            <div class="md:col-1/4">
                                <div class="form-control">
                                    <label for="indirect_cost">Costo indirecto <span class="description">$</span></label>
                                    <text-field name="indirect_cost" v-model="fields.indirect_cost" maxlength="80" ></text-field>
                                    <field-errors name="indirect_cost"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-2">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="subtotal">Costo base <span class="description">$</span></label>
                                    <text-field disabled name="subtotal" v-model="fields.subtotal" maxlength="80" type="number"></text-field>
                                    <field-errors name="subtotal"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="utility">Utilidad <span class="description">%</span></label>
                                    <text-field name="utility" v-model="fields.utility" maxlength="80"></text-field>
                                    <field-errors name="utility"></field-errors>
                                </div>
                            </div>
                        
                        </div>
                        <div class="md:row mb-2">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="costo_total">Subtotal <span class="description">$</span></label>
                                    <text-field disabled name="costo_total" v-model="fields.costo_total" maxlength="80" type="number"></text-field>
                                    <field-errors name="costo_total"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="costo_venta">Costo total de venta <span class="description">$</span></label>
                                    <text-field disabled name="costo_venta" v-model="fields.costo_venta" maxlength="80"></text-field>
                                    <field-errors name="costo_venta"></field-errors>
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
                
            </product-form>
    </div>
</section>

@endsection
