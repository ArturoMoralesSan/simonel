@extends('layout.master')

{{-- Metadata --}}
@section('title', config('app.name'))
@section('description', '')
@section('canonical', config('app.url'))
@section('class', 'home')
@section('content')
    <section class="section">
        <div class="container">
            <h2 class="h2 text-center mb-8">Cambiar contraseña</h2>
            @alert(['class' => 'alert--has-icon size-caption'])
            @endalert

            <div>
                <base-form action="{{ url('cambiar-contrasena') }}" inline-template>
                    <form>
                        <div class="form-group">
                            <div class="form-group__title">
                                Contraseña
                            </div>
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
                        </div>
                        <div class="text-center">
                            <form-button class="btn--success">
                                Actualizar
                            </form-button>
                        </div>
                    </form>
                </base-form>
            </div>
        </div>
    </section>
@endsection