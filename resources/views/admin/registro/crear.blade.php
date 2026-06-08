@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar participante')
@section('tab_title', 'Agregar participante | ' . config('app.name'))
@section('description', 'Agregar participante.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Agregar participante
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/carrera-participantes/') }}">Ver todos los participantes</a>
        </p>

            <base-form action="{{ url('admin/carrera-participantes/crear') }}"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
            <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del participante
                        </h3>

                        <div class="md:row mb-4">
                            <div class="col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="race_id">Carrera</label>
                                    <select-field 
                                        name="race_id" 
                                        v-model="fields.race_id" 
                                        :options="{{ $races }}"
                                        initial=""
                                    >
                                    </select-field>
                                    <field-errors name="race_id"></field-errors>
                                </div>
                            </div>
                            <div class="col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="branch_id">Sucursal</label>
                                    <select-field 
                                        name="branch_id" 
                                        v-model="fields.branch_id" 
                                        :options="{{ $branches }}"
                                        initial=""
                                    >
                                    </select-field>
                                    <field-errors name="race_id"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="name">Nombre completo</label>
                                    <text-field name="name" v-model="fields.name" maxlength="80" initial=""></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="age">Edad</label>
                                    <span class="description">
                                        Opcional
                                    </span>
                                    <text-field name="age" v-model="fields.age" maxlength="80" initial=""></text-field>
                                    <field-errors name="age"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="sex">Sexo</label>
                                    <select-field 
                                        name="sex" 
                                        v-model="fields.sex" 
                                        :options="{'M': 'Masculino', 'F': 'Femenino' }"
                                        initial=""
                                    >
                                    </select-field>
                                    <field-errors name="sex"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="size">Talla</label>
                                    <select-field 
                                        name="size" 
                                        v-model="fields.size" 
                                        :options="{'XS': 'XS', 'S': 'S', 'M':'M', 'L':'L', 'XL': 'XL'  }"
                                        initial=""
                                    >
                                    </select-field>
                                    <field-errors name="size"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="cellphone">Celular</label>
                                    <span class="description">
                                        Opcional
                                    </span>
                                    <text-field name="cellphone" v-model="fields.cellphone" maxlength="80" initial=""></text-field>
                                    <field-errors name="cellphone"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="type">Tipo de carrera</label>
                                    <select-field 
                                        name="type" 
                                        v-model="fields.type" 
                                        :options="{'5KM':'5 KM', '10KM': '10 KM'  }"
                                        initial=""
                                    >
                                    </select-field>
                                    <field-errors name="type"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                {{-- nombres --}}
                                <div class="form-control">
                                    <label for="cost">Pago</label>
                                    <text-field name="cost" v-model="fields.cost" maxlength="80" initial=""></text-field>
                                    <field-errors name="cost"></field-errors>
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
