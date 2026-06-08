@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('meta.title', 'Cambiar contraseña' )
@section('meta.tab_title', 'Cambiar contraseña | Panel de administración | ' . config('app.name'))
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Cambiar contraseña
        </h1>
    </div>
    
    <div class="fluid-container">
        @include('components.alert')
        <section class="db-panel mx-auto sm:col-2/3 md:col-1/2">
            <h3 class="db-panel__title">
                Contraseña
            </h3>
            

            <base-form action="{{ url('admin/perfil/cambiar-contrasena') }}" inline-template>
                <form>
                    {{-- Current password --}}
                    <div class="form-control">
                        <label for="current-password">Contraseña actual</label>

                        <text-field v-model="fields.current_password"
                            name="current_password"
                            maxlength="60"
                            type="password"
                        ></text-field>

                        <field-errors name="current_password"></field-errors>
                    </div>

                    {{-- New password --}}
                    <div class="form-control">
                        <label for="password">Contraseña nueva</label>

                        <text-field v-model="fields.password"
                            name="password"
                            maxlength="60"
                            type="password"
                            aria-describedby="password-specs"
                        ></text-field>

                        <field-errors name="password"></field-errors>

                        <p id="password-specs" class="description">
                            Mínimo 6 caracteres
                        </p>
                    </div>

                    {{-- Password confirmation --}}
                    <div class="form-control">
                        <label for="password-confirmation">Confirmar contraseña</label>

                        <text-field v-model="fields.password_confirmation"
                            name="password_confirmation"
                            maxlength="60"
                            type="password"
                        ></text-field>

                        <field-errors name="password_confirmation"></field-errors>
                    </div>

                    <div class="text-center">
                        <form-button class="btn btn--blue--dashboard btn--wide"></form-button>
                    </div>
                </form>
            </base-form>




        </section>
    </div>
@endsection
