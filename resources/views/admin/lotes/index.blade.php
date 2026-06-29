@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Lotes')
@section('tab_title', 'Lotes | ' . config('app.name'))
@section('description', 'Lista de Lotes.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Lotes
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $lots->count() }} lotes registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <form-search
            selected="{{ request('search') }}"
        >
            <template slot="svg-search">
                <img
                    class="search-form_icon"
                    src="{{ url('img/svg/search.svg') }}"
                    alt=""
                >
            </template>
        </form-search>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de lotes
            </h3>

            @if (!$lots->count())
                <p class="text-center py-1">
                    Por el momento no hay lotes registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $lots }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Materia prima</th>
                                <th>Almacén</th>
                                <th>Numero de lote</th>
                                <th>Proveedor</th>
                                <th>Fecha de entrada</th>
                                <th>Fecha de caducidad</th>
                                <th>Cantidad recibida</th>
                                <th>Existencia actual</th>
                                <th>Costo</th>
                                <th>Estado</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="lotItem in resourceList" class="table-resource__row" :key="lotItem.id">
                                <td data-label="Materia prima:">
                                    @{{ lotItem.material.name }}
                                </td>
                                <td data-label="Almacén:">
                                    @{{ lotItem.warehouse.name }} <span class="description">(@{{ lotItem.warehouse.warehouse_type }})</span>
                                </td>
                                <td data-label="Número de lote:">
                                    @{{ lotItem.lot_number }}
                                </td>

                                <td data-label="Proveedor:">
                                     @{{ lotItem.supplier ? lotItem.supplier.business_name : 'Sin proveedor' }}
                                </td>
                                <td data-label="Fecha de entrada:">
                                    @{{ lotItem.formated_entry_date }}
                                </td>
                                <td data-label="Fecha de caducidad:">
                                    @{{ lotItem.formated_expiration_date }}
                                </td>
                                <td data-label="Cantidad recibida:">
                                    @{{ lotItem.initial_quantity }}
                                </td>
                                <td data-label="Existencia actual:">
                                    @{{ lotItem.available_quantity }}
                                </td>
                                <td data-label="Costo:">
                                    $@{{ lotItem.cost }}
                                </td>
                                <td data-label="Estado:">
                                    @{{ lotItem.status }}
                                </td>
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/lotes/' + lotItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/lotes/eliminar/' + lotItem.id"
                                        :resource-id="lotItem.id"
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
