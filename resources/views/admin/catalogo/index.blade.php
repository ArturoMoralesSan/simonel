@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Catálogo de productos')
@section('tab_title', 'Catálogo de productos | ' . config('app.name'))
@section('description', 'Lista de Catálogo  de productos.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Catálogo de productos
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $catalogo->count() }} productos registrados.
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
                Lista de productos
            </h3>

            @if (! $catalogo->count())
                <p class="text-center py-1">
                    Por el momento no hay productos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $catalogo }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Nombre</th>
                                <th>Descripción</th>
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
                                

                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/catalogo/' + productItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button 
                                        class="btn--danger table-resource__button" :url="$root.path + '/admin/catalogo/eliminar/' + productItem.id"
                                        :resource-id="productItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                        :disabled="productItem.recipes_count != 0"
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
