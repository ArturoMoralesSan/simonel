@extends('layout.master')

{{-- Metadata --}}
@section('title', config('app.name'))
@section('description', '')
@section('canonical', config('app.url'))
@section('class', 'home')
@section('content')
    <section class="mt-16">
        <h1 class="h2 text-center mb-8">Padrón Municipal de Agentes Culturales</h1>
    </section>
    <section class="section section-welcome" style="border-bottom: 1px solid #dcdcdc;">
        <div class="container">
            <p>
                Bienvenido al Padrón Municipal de Agentes Culturales, es muy importante para nosotros conocer tu actividad y aportes a la Cultura de Durango, con ello logramos tener la información real de aquellos que dedican su tiempo y conocimiento a enriquecer el quehacer cultural de nuestra comunidad.
            </p>
            <p>
                Este padrón representa un censo específico de los actores culturales, clasificando en él tu actividad, categoría de desarrollo y trayectoria, a su vez, esta herramienta te facilitará acceder a beneficios municipales creados para incentivar, reconocer y fomentar tu desarrollo.
            </p>
            <p>
                <strong>Requisitos y criterios de incorporación:</strong>
            </p>

            <ol class="list-spaced">
                <li>
                    Haber nacido o residir en el Municipio de Durango por período comprobable de al menos dos años a la fecha de la solicitud de incorporación.
                </li>
                <li>
                    Ser artista profesional, promotor o gestor cultural.
                </li>
                <li>
                    Ingresar en la plataforma los datos requeridos.
                </li>
            </ol>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <h2 class="h2 text-center mb-8">Registro</h2>
            @alert(['class' => 'alert--has-icon size-caption'])
            @endalert

            <div>
                <login-form action="{{ url('registro-artistas') }}"
                    enctype="multipart/form-data"
                    inline-template
                    v-cloak
                >
                    <form>
                        <div class="form-group">
                            <div class="form-group__title">
                                Datos personales
                            </div>
                            <div class="form-control">
                                <label for="full_name">Nombre completo</label>
                                <text-field name="full_name" v-model="fields.full_name" maxlength="160" placeholder="" initial=""></text-field>
                                <field-errors name="full_name"></field-errors>
                            </div>
                            <div class="form-control">
                                <label for="email">Correo electrónico</label>
                                <text-field name="email" type="email" v-model="fields.email" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="email"></field-errors>
                            </div>
                            <div class="row">
                                <div class="col lg:col-1/2 form-control">
                                    <label for="telephone">Teléfono</label>
                                    <text-field name="telephone" v-model="fields.telephone" maxlength="10" placeholder="" initial=""></text-field>
                                    <field-errors name="telephone"></field-errors>
                                </div>
                                <div class="col lg:col-1/2 form-control">
                                    <label for="cellphone">Celular</label>
                                    <text-field name="cellphone" v-model="fields.cellphone" maxlength="10" placeholder="" initial=""></text-field>
                                    <field-errors name="cellphone"></field-errors>
                                </div>
                            </div>
                            <div class="form-control">
                                <label for="address">Dirección</label>
                                <text-field name="address" v-model="fields.address" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="address"></field-errors>
                            </div>
                            <div class="form-control">
                                <label for="photo" v-text="'Fotografia artística'"></label>
                                <file-field name="photo" v-model="fields.photo" aria-describedby="photo-specs"></file-field>
                                <field-errors name="photo"></field-errors>
                                <ul id="photo-specs" class="description">
                                    <li>
                                        Sólo imágenes de tipo: jpeg, gif, png.
                                    </li>
                                    <li>
                                         El archivo no debe exceder 2 MB.
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group__title">
                                Datos profesionales
                            </div>
                            <div class="row">
                                <div class="col lg:col-1/2 form-control">
                                    <label for="activity">Actividad en la que se desarrolla principalmente:</label>
                                    <select-field name="activity"  v-model="fields.activity" :options="{{ $activities }}">
                                    </select-field>
                                    <field-errors name="activity"></field-errors>
                                </div>
                                <div class="col lg:col-1/2 form-control">
                                    <label for="category">Categoría:</label>
                                    <select-field name="category"  v-model="fields.category" :options="{{ $categories}}">
                                    </select-field>
                                    <field-errors name="category"></field-errors>
                                </div>
                            </div>
                            <div class="col">
                                <label for="speciality">Especialidad:</label>
                                <text-field name="speciality" v-model="fields.speciality" maxlength="120" placeholder="" initial=""></text-field>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group__title">
                                Redes sociales
                            </div>
                            <div class="form-control">
                                <label for="facebook">Facebook</label>
                                <span class="description">
                            Opcional
                        </span>
                                <text-field name="facebook" v-model="fields.facebook" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="facebook"></field-errors>
                                <ul id="facebook-specs" class="description">
                                    <li>
                                        El enlace debe de ser una url valida por ejemplo http://www.ejemplo.com
                                    </li>
                                </ul>
                            </div>
                            <div class="form-control">
                                <label for="twitter">Twitter</label>
                                <span class="description">
                            Opcional
                        </span>
                                <text-field name="twitter" v-model="fields.twitter" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="twitter"></field-errors>
                                <ul id="twitter-specs" class="description">
                                    <li>
                                        El enlace debe de ser una url valida por ejemplo http://www.ejemplo.com
                                    </li>
                                </ul>
                            </div>
                            <div class="form-control">
                                <label for="instagram">Instagram</label>
                                <span class="description">
                            Opcional
                        </span>
                                <text-field name="instagram" v-model="fields.instagram" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="instagram"></field-errors>
                                <ul id="instagram-specs" class="description">
                                    <li>
                                        El enlace debe de ser una url valida por ejemplo http://www.ejemplo.com
                                    </li>
                                </ul>
                            </div>
                            <div class="form-control">
                                <label for="youtube">Youtube</label>
                                <span class="description">
                            Opcional
                        </span>
                                <text-field name="youtube" v-model="fields.youtube" maxlength="60" placeholder="" initial=""></text-field>
                                <field-errors name="youtube"></field-errors>
                                <ul id="youtube-specs" class="description">
                                    <li>
                                        El enlace debe de ser una url valida por ejemplo http://www.ejemplo.com
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="sintax">Semblanza</label>
                            <text-area name="sintax" cols="30" rows="10" v-model="fields.sintax" maxlength="10000" initial=""></text-area>
                            <field-errors name="sintax"></field-errors>
                        </div>
                        <div class="text-center">
                            <form-button class="btn--success">
                                Registrar
                            </form-button>
                        </div>
                    </form>
                </login-form>
            </div>
        </div>
    </section>
@endsection