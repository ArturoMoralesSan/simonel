@extends('layout.master')

{{-- Metadata --}}
@section('title', config('app.name'))
@section('description', '')
@section('canonical', config('app.url'))
@section('class', 'home')
@section('content')
    <section class="section">
        <div class="container">
            <h2 class="h2 text-center mb-8">Editar perfil</h2>
            @alert(['class' => 'alert--has-icon size-caption'])
            @endalert

            <div>
                <login-form action="{{ url('editar-perfil') }}"
                    method="PUT"
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
                                <text-field name="full_name" v-model="fields.full_name" maxlength="160" placeholder="" initial="{{ $authUser->full_name }}"></text-field>
                                <field-errors name="full_name"></field-errors>
                            </div>
                            <div class="form-control">
                                <label for="email">Correo electrónico</label>
                                <text-field name="email" type="email" v-model="fields.email" maxlength="60" placeholder="" initial="{{ $authUser->email }}"></text-field>
                                <field-errors name="email"></field-errors>
                            </div>
                            <div class="row">
                                <div class="col lg:col-1/2 form-control">
                                    <label for="telephone">Teléfono</label>
                                    <text-field name="telephone" v-model="fields.telephone" maxlength="10" placeholder="" initial="{{ $authUser->artists->telephone }}"></text-field>
                                    <field-errors name="telephone"></field-errors>
                                </div>
                                <div class="col lg:col-1/2 form-control">
                                    <label for="cellphone">Celular</label>
                                    <text-field name="cellphone" v-model="fields.cellphone" maxlength="10" placeholder="" initial="{{ $authUser->artists->cellphone }}"></text-field>
                                    <field-errors name="cellphone"></field-errors>
                                </div>
                            </div>
                            <div class="row">
                                <div class="lg:col-2/3">
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
                                <div class="lg:col-1/3">
                                    <img src="{{ url('archivos/artistas/'.$authUser->photo) }}" class="img-detail" alt="">
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="form-group__title">
                                Datos profesionales
                            </div>
                            <div class="row">
                                <div class="col lg:col-1/2 form-control">
                                    <label for="activity">Actividad en la que se desarrolla principalmente:</label>
                                    <select-field name="activity"  v-model="fields.activity" :options="{{ $activities }}" initial="{{ $authUser->artists->activity_id }}">
                                    </select-field>
                                    <field-errors name="activity"></field-errors>
                                </div>
                                <div class="col lg:col-1/2 form-control">
                                    <label for="category">Categoría:</label>
                                    <select-field name="category"  v-model="fields.category" :options="{{ $categories}}" initial="{{ $authUser->artists->category_id }}">
                                    </select-field>
                                    <field-errors name="category"></field-errors>
                                </div>
                            </div>
                            <div class="col">
                                <label for="speciality">Especialidad:</label>
                                <text-field name="speciality" v-model="fields.speciality" maxlength="120" placeholder="" initial="{{ $authUser->artists->speciality }}"></text-field>
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
                                <text-field name="facebook" v-model="fields.facebook" maxlength="60" placeholder="" initial="{{ $authUser->artists->facebook }}"></text-field>
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
                                <text-field name="twitter" v-model="fields.twitter" maxlength="60" placeholder="" initial="{{ $authUser->artists->twitter }}"></text-field>
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
                                <text-field name="instagram" v-model="fields.instagram" maxlength="60" placeholder="" initial="{{ $authUser->artists->instagram }}"></text-field>
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
                                <text-field name="youtube" v-model="fields.youtube" maxlength="60" placeholder="" initial="{{ $authUser->artists->youtube }}"></text-field>
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
                            <text-area name="sintax" cols="30" rows="10" v-model="fields.sintax" maxlength="10000">{{ $authUser->artists->sintax }}</text-area>
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