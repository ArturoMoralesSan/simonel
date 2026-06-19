@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Lote de productos')
@section('tab_title', 'Lote de productos | ' . config('app.name'))
@section('description', 'Lote de productos.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Lotes de productos
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $ProductLotItems->count() }} elementos registrados.
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
                Lotes de productos
            </h3>

            @if (! $ProductLotItems->count())
                <p class="text-center py-1">
                    Por el momento no hay elementos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $ProductLotItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Número de lote</th>
                                <th>Órden de producción</th>
                                <th>Producto</th>
                                <th>Almacen</th>
                                <th>Código de barras</th>
                                <th>Cantidad inicial</th>
                                <th>Cantidad disponible</th>
                                <th>Estado</th>
                                <th>Costo total</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ProductLotItem in resourceList" class="table-resource__row" :key="ProductLotItem.id">
                                <td data-label="Número de lote:">
                                    @{{ ProductLotItem.lot_number }}
                                </td>
                                <td data-label="Órden de producción:">
                                    @{{ ProductLotItem.order.order_number }}
                                </td>
                                <td data-label="Producto:">
                                    @{{ ProductLotItem.product.manufactured.name }}
                                </td>
                                <td data-label="Almacen:">
                                    @{{ ProductLotItem.warehouse.name }}
                                </td>
                                <td data-label="Código de barras:">
                                    @{{ ProductLotItem.warehouse.name }}
                                </td>
                                 <td data-label="Cantidad inicial:">
                                    @{{ ProductLotItem.initial_quantity }}
                                </td>
                                <td data-label="Cantidad disponible:">
                                    @{{ ProductLotItem.available_quantity }}
                                </td>
                                <td data-label="Estado:">
                                    @{{ ProductLotItem.status }}
                                </td>
                                <td data-label="Costo total:">
                                    $@{{ ProductLotItem.total_cost }}
                                </td>
                                
                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/lotes-producto/' + ProductLotItem.id + '/detalle' ">
                                       <img
                                            class="svg-icon-only"
                                            src="{{ url('img/svg/order.svg')}}"
                                        >
                                        Detalle
                                    </a>
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/lotes-producto/' + ProductLotItem.id + '/etiqueta' ">
                                        <img
                                            class="svg-icon-only"
                                            src="{{ url('img/svg/order.svg')}}"
                                        >
                                        Etiqueta
                                    </a>
                                    <!-- <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/inventario/eliminar/' + ProductLotItem.id"
                                        :resource-id="ProductLotItem.id"
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
