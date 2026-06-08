@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Inventario')
@section('tab_title', 'Inventario | ' . config('app.name'))
@section('description', 'Inventario.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Inventario
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $inventoriesItems->count() }} elementos registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">

        <form-search 
                selected="{{ app('request')->input('search') }}"
            >
            <template slot="svg-search">
                <img class="search-form_icon" src="{{ url('img/svg/search.svg') }}" alt="">
            </template>
        </form-search>
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Inventario
            </h3>

            @if (! $inventoriesItems->count())
                <p class="text-center py-1">
                    Por el momento no hay elementos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $inventoriesItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Cliente</th>
                                <th>Cantidad de inventarios</th>
                                <th>Productos en inventario</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inventoryItem in resourceList" class="table-resource__row" :key="inventoryItem.client.id">
                                <td data-label="Producto:">
                                    @{{ inventoryItem.client.name }} @{{ inventoryItem.client.last_name }}
                                </td>
                                <td data-label="Cantidad de inventarios:">
                                    @{{ inventoryItem.count }}
                                </td>
                                <td data-label="Productos en inventario:">
                                    @{{ inventoryItem.inventories }}
                                </td>
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/inventario-clientes/' + inventoryItem.client.id + '/detalle' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Detalles
                                    </a>
                                    <!-- <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/inventario/eliminar/' + inventoryItem.id"
                                        :resource-id="inventoryItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                        Eliminar
                                    </delete-button> -->
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>
                {!! $links !!}
            @endif

        </section>
    </div>
@endsection
