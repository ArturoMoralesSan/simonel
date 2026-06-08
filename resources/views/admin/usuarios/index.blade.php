@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Usuarios')
@section('tab_title', 'Usuarios | ' . config('app.name'))
@section('description', 'Lista de usuarios.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Usuarios
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $users->count() }} usuarios registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de usuarios
            </h3>

            @if (! $users->count())
                <p class="text-center py-1">
                    Por el momento no hay usuarios registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $users }}" inline-template>

                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Nombre completo</th>
                                <th>Correo Electrónico</th>
                                <th>Rol</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="userItem in resourceList" class="table-resource__row" :key="userItem.id">
                                <td data-label="Folio:">
                                    <a class="" :href="$root.path + '/admin/usuarios/' + userItem.id + '/editar'">
                                        @{{ userItem.name }} @{{ userItem.last_name }}
                                    </a>
                                </td>
                                <td data-label="Correo electrónico:">
                                    @{{ userItem.email }}
                                </td>
                                <td data-label="Roles:">
                                    @{{ userItem.role.name }}
                                </td>
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/usuarios/' + userItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>

                                    <delete-button class="btn--danger table-resource__button" v-if="userItem.id != {{ Auth::user()->id }}" :url="$root.path + '/admin/usuarios/eliminar/' + userItem.id"
                                        :resource-id="userItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img style="width: 15px;" class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                        Eliminar
                                    </delete-button>
                                    


                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>

            @endif

        </section>
    </div>
@endsection
