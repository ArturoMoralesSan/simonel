@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('meta.title', 'Actualizar perfil' )
@section('meta.tab_title', 'Actualizar perfil | Panel de administración | ' . config('app.name'))
@section('css_classes', 'dashboard')
@section('has_gallery', 'true')

@section('content')

    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Actualizar perfil
        </h1>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')

        <profile-form action="{{ url('admin/perfil/editar') }}"
            method="put"
            avatar="{{ $authUser->avatar }}"
            enctype="multipart/form-data"
            inline-template
            v-cloak
        >
            <form>
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Datos del usuario
                    </h3>

                    <div class="md:row mb-4">
                        <div class="md:col-1/2">

                            {{-- Name --}}
                            <div class="form-control">
                                <label for="name">Nombre </label>
                                <text-field name="name" v-model="fields.name" maxlength="160" initial="{{ $authUser->name }}"></text-field>

                                <field-errors name="name"></field-errors>
                            </div>
                        </div>
                        <div class="md:col-1/2">

                            {{-- Name --}}
                            <div class="form-control">
                                <label for="last_name">Apellidos</label>
                                <text-field name="last_name" v-model="fields.last_name" maxlength="160" initial="{{ $authUser->last_name }}"></text-field>

                                <field-errors name="last_name"></field-errors>
                            </div>
                        </div>
                    </div>
                    <div class="md:row mb-4">
                        <div class="md:col">

                            {{-- email --}}
                            <div class="form-control">
                                <label for="email">Correo Electrónico </label>
                                <text-field name="email" v-model="fields.email" maxlength="160" initial="{{ $authUser->email }}"></text-field>

                                <field-errors name="email"></field-errors>
                            </div>
                        </div>
                        
                    </div>
                </section>
                <!-- <section class="db-panel mb-16">
                    <h3 class="db-panel__title">
                        Avatar
                    </h3>
                    <div class="md:row">
                        <div class="preview-aside">
                            <figure class="preview-aside__box preview-box">
                                <profile-gallery-card
                                    :link="$root.path + '/archivos/avatars/' + (hasAvatar ? avatar : 'default.png')"
                                    :thumb="$root.path + '/archivos/avatars/' + (hasAvatar ? avatar : 'default.png')"
                                    :images="[{
                                        src: $root.path + '/archivos/avatars/' + (hasAvatar ? avatar : 'default.png'),
                                        w: 68,
                                        h: 68
                                    }]"
                                    inline-template
                                >
                                    <a target="_blank" :href="link" @click.prevent="onClick">
                                        <img class="preview-box__img" :src="thumb" alt="" ref="thumb">
                                    </a>
                                </profile-gallery-card>

                                <figcaption class="preview-box__caption">
                                    Imagen actual
                                </figcaption>
                            </figure>
                        </div>

                        <div class="md:col-2/3">
                            {{-- Avatar --}}
                            <div class="form-control">
                                <label for="avatar" v-text="hasAvatar ? 'Reemplazar imagen' : 'Agregar imagen'"></label>

                                <file-field name="avatar" v-model="fields.avatar"></file-field>

                                <field-errors name="avatar"></field-errors>
                                <ul id="avatar-specs" class="description color-gray-darken-1">
                                    <li>
                                        Tamaño máximo: 1 MB.
                                    </li>
                                    <li>
                                        Sólo archivos con extensión jpeg, gif, png.
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </section> -->

                <div class="text-center">
                    <form-button class="btn btn--blue--dashboard btn--wide">
                        Actualizar
                    </form-button>
                </div>
            </form>
        </profile-form>

    </div>

@endsection
