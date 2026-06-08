@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar productos de inventario')
@section('tab_title', 'Editar productos de inventario | ' . config('app.name'))
@section('description', 'Editar productos de inventario.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Editar productos de inventario
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/inventario/') }}">Ver todo los elementos del inventario</a>
            </p>

            <base-form action="{{ url('admin/inventario/' . $inventory->id . '/actualizar') }}" method="put"
                enctype="multipart/form-data" inline-template v-cloak>
                <form>
                <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del producto
                        </h3>

                        <div class="md:row mb-4">
                            <div class="md:col">
                                <div class="form-control">
                                    <label for="product">Producto</label>
                                    <select-field class="form-select" name="product_id" v-model="fields.product_id"
                                        :options="{{ $products }}"
                                        initial="{{ $inventory->product_id }}"
                                        >
                                    </select-field>
                                    <field-errors name="product_id"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="quantity_min">Cantidad mínima</label>
                                    <text-field name="quantity_min" v-model="fields.quantity_min" initial="{{ $inventory->quantity_min }}"></text-field>
                                    <field-errors name="quantity_min"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="quantity">Cantidad en existencia</label>
                                    <text-field name="quantity" v-model="fields.quantity" initial="{{ $inventory->quantity }}"></text-field>
                                    <field-errors name="quantity"></field-errors>
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
