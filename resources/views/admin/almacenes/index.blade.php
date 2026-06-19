@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Almacen')
@section('tab_title', 'Almacen | ' . config('app.name'))
@section('description', 'Lista de almacen.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Almacen
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $warehouses->count() }} Almacenes registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de almacenes
            </h3>

            @if (!$warehouses->count())
                <p class="text-center py-1">
                    Por el momento no hay almacenes registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $warehouses }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Almacen</th>
                                <th>Tipo de almacen</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="warehouseItem in resourceList" class="table-resource__row" :key="warehouseItem.id">
                                
                                <td data-label="Nombre:">
                                    @{{ warehouseItem.name }}
                                </td>
                                <td data-label="Tipo de almacen:">
                                    @{{ warehouseItem.warehouse_type }}
                                </td>
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/almacenes/' + warehouseItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/almacenes/eliminar/' + warehouseItem.id"
                                        :resource-id="warehouseItem.id"
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

            @endif

        </section>
    </div>
@endsection
