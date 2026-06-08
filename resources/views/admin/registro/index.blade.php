@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'participantes')
@section('tab_title', 'participantes | ' . config('app.name'))
@section('description', 'Lista de participantes.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            participantes
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $registers->count() }} participantes registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de participantes
            </h3>

            @if (! $registers->count())
                <p class="text-center py-1">
                    Por el momento no hay participantes registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $registersItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Sucursal</th>
                                <th>Sexo</th>
                                <th>Talla</th>
                                <th>Celular</th>
                                <th>Tipo de carrera</th>
                                <th>Monto</th>

                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="registersItem in resourceList" class="table-resource__row" :key="registersItem.id">
                                <td data-label="Fecha:">
                                    @{{ registersItem.formated_date }}
                                </td>
                                <td data-label="Nombre:">
                                    @{{ registersItem.name }}
                                </td>
                                <td data-label="Edad:">
                                    @{{ registersItem.age }}
                                </td>
                                <td data-label="Sucursal:">
                                    @{{ registersItem.branch.name }}
                                </td>
                                <td data-label="Sexo:">
                                    @{{ registersItem.sex }}
                                </td>
                                <td data-label="Talla:">
                                    @{{ registersItem.size }}
                                </td>
                                <td data-label="Celular:">
                                    @{{ registersItem.cellphone }}
                                </td>
                                <td data-label="Tipo de carrera:">
                                    @{{ registersItem.type }}
                                </td>
                                <td data-label="Monto:">
                                   $ @{{ registersItem.amount }}
                                </td>
                                
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/carrera-participantes/' + registersItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/carrera-participantes/eliminar/' + registersItem.id"
                                        :resource-id="registersItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                        Eliminar
                                    </delete-button>
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>

                {{ $registers->links('layout.pagination')}}
                

            @endif

        </section>
    </div>
@endsection
