@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar proveedor')
@section('tab_title', 'Editar proveedor | ' . config('app.name'))
@section('description', 'Editar proveedor.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar proveedor
        </h1>
    </div>

    <div class="fluid-container mb-16">

        <p class="mb-12">
            @include('components.alert')

            <span class="color-link">«</span>
            <a href="{{ url('admin/proveedores/') }}">
                Ver todos los proveedores
            </a>
        </p>

        <base-form
            action="{{ url('admin/proveedores/' . $supplier->id . '/actualizar') }}"
            method="PUT"
            enctype="multipart/form-data"
            inline-template
            v-cloak
        >
            <form>

                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Datos del proveedor
                    </h3>

                    <div class="md:row">

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>Razón social *</label>

                                <text-field
                                    name="business_name"
                                    v-model="fields.business_name"
                                    maxlength="255"
                                    initial="{{ $supplier->business_name }}"
                                >
                                </text-field>

                                <field-errors name="business_name"></field-errors>
                            </div>
                        </div>

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>Nombre comercial</label>

                                <text-field
                                    name="trade_name"
                                    v-model="fields.trade_name"
                                    maxlength="255"
                                    initial="{{ $supplier->trade_name }}"
                                >
                                </text-field>

                                <field-errors name="trade_name"></field-errors>
                            </div>
                        </div>

                    </div>

                    <div class="md:row">

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>Contacto</label>

                                <text-field
                                    name="contact_name"
                                    v-model="fields.contact_name"
                                    maxlength="255"
                                    initial="{{ $supplier->contact_name }}"
                                >
                                </text-field>

                                <field-errors name="contact_name"></field-errors>
                            </div>
                        </div>

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>RFC</label>

                                <text-field
                                    name="rfc"
                                    v-model="fields.rfc"
                                    maxlength="20"
                                    initial="{{ $supplier->rfc }}"
                                >
                                </text-field>

                                <field-errors name="rfc"></field-errors>
                            </div>
                        </div>

                    </div>

                    <div class="md:row">

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>Teléfono</label>

                                <text-field
                                    name="phone"
                                    v-model="fields.phone"
                                    maxlength="50"
                                    initial="{{ $supplier->phone }}"
                                >
                                </text-field>

                                <field-errors name="phone"></field-errors>
                            </div>
                        </div>

                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>Correo electrónico</label>

                                <text-field
                                    name="email"
                                    v-model="fields.email"
                                    maxlength="255"
                                    initial="{{ $supplier->email }}"
                                >
                                </text-field>

                                <field-errors name="email"></field-errors>
                            </div>
                        </div>

                    </div>

                    <div class="form-control">
                        <label>Dirección</label>

                        <text-area
                            name="address"
                            v-model="fields.address"
                        >{{ $supplier->address }}
                        </text-area>

                        <field-errors name="address"></field-errors>
                    </div>

                    <div class="form-control">
                        <label>Notas</label>

                        <text-area
                            name="notes"
                            v-model="fields.notes"
                        >{{ $supplier->notes }}
                        </text-area>

                        <field-errors name="notes"></field-errors>
                    </div>

                </section>

                <div class="text-center">
                    <form-button class="btn--primary btn--wide">
                        Actualizar
                    </form-button>
                </div>

            </form>
        </base-form>

    </div>
</section>

@endsection