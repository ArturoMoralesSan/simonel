@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Agregar usuarios')
@section('tab_title', 'Agregar usuarios | ' . config('app.name'))
@section('description', 'Agregar usuarios.')
@section('css_classes', 'dashboard')

@section('content')

    <section class="mb-16">
        <div class="dashboard-heading">
            <h1 class="dashboard-heading__title">
                Agregar Usuario
            </h1>
        </div>

        <div class="fluid-container mb-16">
            <p class="mb-12">
                @include('components.alert')
                <span class="color-link">«</span>
                <a href="{{ url('admin/usuarios/') }}">Ver todos los usuarios</a>
            </p>

            <base-form action="{{ url('admin/usuarios/crear') }}" enctype="multipart/form-data" inline-template v-cloak>
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del usuario
                        </h3>

                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name"></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="last_name">Apellidos</label>
                                    <text-field name="last_name" v-model="fields.last_name"></text-field>
                                    <field-errors name="last_name"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <label for="username">Nombre de usuario</label>
                                    <text-field name="username" v-model="fields.username"></text-field>
                                    <field-errors name="username"></field-errors>
                                </div>
                            </div>
                            
                            <div class="md:col-1/2">
                                <div class="form-control">
                                    <div class="form-control">
                                    <label for="email">Correo electrónico</label>
                                    <text-field type="email" name="email" v-model="fields.email"></text-field>
                                    <field-errors name="email"></field-errors>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="md:row mb-4">
                            <div class="md:col">
                                <div class="form-control">
                                    <label for="role">Rol</label>
                                    <select-field class="form-select" name="role" v-model="fields.role_id"
                                        :options="{{ $roles }}">
                                    </select-field>
                                    <field-errors name="roles"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="password">Contraseña</label>
                            <text-field name="password" v-model="fields.password"></text-field>
                            <field-errors name="password"></field-errors>
                        </div>
                    </section>
                    <div class="text-center">
                        <form-button class="btn--success btn--wide">
                            Agregar
                        </form-button>
                    </div>
                </form>
            </base-form>
        </div>
    </section>

@endsection
