@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'productos')
@section('tab_title', 'productos | ' . config('app.name'))
@section('description', 'Lista de productos.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Productos
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $ProductsItems->count() }} productos registrados.
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
                Lista de productos
            </h3>

            @if (!$ProductsItems->count())
                <p class="text-center py-1">
                    Por el momento no hay productos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $ProductsItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Costo</th>
                                <th>Costo de presentación</th>
                                <th>Costo indirecto</th>
                                <th>Costo base</th>
                                <th>Utilidad</th>
                                <th>Subtotal</th>
                                <th>Costo total de venta</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="productItem in resourceList" class="table-resource__row" :key="productItem.id">
                                <td data-label="Nombre:">
                                    @{{ productItem.name }}
                                </td>
                                <td data-label="Descripción:">
                                    @{{ productItem.description }}
                                </td>
                                <td data-label="Tipo de impresión :">
                                    @{{ productItem.type.name }}
                                </td>
                                <td data-label="Costo:">
                                    $@{{ productItem.vinil_cost }} 
                                </td>
                                <td data-label="Costo de empaquetado:">
                                    $@{{ productItem.impresion_cost }} <span class="description">@{{ productItem.cut.name }}(@{{ productItem.cut.measure }})</span> 
                                </td>
                                <td data-label="Costo indirecto:">
                                    $@{{ productItem.indirect_cost }}
                                </td>
                                <td data-label="Costo base:">
                                    $@{{ productItem.subtotal }}
                                </td>
                                <td data-label="Utilidad:">
                                    @{{ productItem.utility }}%
                                </td>
                                <td data-label="Subtotal:">
                                    $@{{ productItem.costo_total }}
                                </td>
                                <td data-label="Costo total de venta:">
                                    $@{{ productItem.costo_venta }}
                                </td>


                                <td class="table-resource__actions" data-label="Acciones:">
                                    <clone-button 
                                        class="btn btn-nowrap btn--sm btn--success table-resource__button mr-2" 
                                        :item="productItem" 
                                        :url="$root.path + '/admin/productos/clonar/' + productItem.id" 
                                        @clone="cloneResource"
                                    >
                                        <img class="svg-icon" src="{{ url('img/svg/clone.svg')}}">
                                        Clonar
                                    </clone-button>
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/productos/' + productItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/productos/eliminar/' + productItem.id"
                                        :resource-id="productItem.id"
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

                {!! $links !!}

            @endif

        </section>
    </div>
@endsection
