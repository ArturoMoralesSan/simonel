@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Inventario por tipo de almacén')
@section('tab_title', 'Inventario por tipo de almacén | ' . config('app.name'))
@section('description', 'Inventario por tipo de almacén.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Inventario por tipo de almacén
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
                Inventario de almacén
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
                                <th>Almacén</th>
                                <th>Cantidad en inventario</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inventoryItem in resourceList" class="table-resource__row" :key="inventoryItem.id">
                                <td data-label="Producto:">
                                    @{{ inventoryItem.product.name }} <span class="description">(@{{ inventoryItem.product.cut.name }} @{{ inventoryItem.product.cut.measure }} )</span>
                                </td>
                                <
                                <td data-label="Cantidad en inventario:">
                                    @{{ inventoryItem.quantity }}
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
