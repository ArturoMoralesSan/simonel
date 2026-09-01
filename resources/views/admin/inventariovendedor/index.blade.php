@extends('layout.dashboard-master')

@section('title', 'Inventario de vendedores')

@section('tab_title', 'Inventario de vendedores | ' . config('app.name'))

@section('description', 'Inventario asignado a vendedores.')

@section('css_classes', 'dashboard')

@section('content')

    <div class="dashboard-heading">

        <h1 class="dashboard-heading__title">
            Inventario de vendedores
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $sellersItems->count() }} elementos registrados.
        </p>

    </div>

    <div class="fluid-container mb-16">

        <form-search
            selected="{{ app('request')->input('search') }}"
        >
            <template slot="svg-search">
                <img
                    class="search-form_icon"
                    src="{{ url('img/svg/search.svg') }}"
                    alt=""
                >
            </template>
        </form-search>

        @include('components.alert')

        <section class="db-panel">

            <h3 class="db-panel__title">
                Inventario de vendedores
            </h3>

            @if (! $sellersItems->count())

                <p class="text-center py-1">
                    Por el momento no hay vendedores con inventario asignado.
                </p>

            @else

                <resource-table
                    :breakpoint="800"
                    :model="{{ $sellersItems }}"
                    inline-template
                >

                    <table class="table size-caption mx-auto mb-16 md:table--responsive">

                        <thead>

                            <tr class="table-resource__headings">

                                <th>Vendedor</th>

                                <th>Cantidad de asignaciones</th>

                                <th class="pr-4">
                                    Acciones
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="sellerItem in resourceList"
                                class="table-resource__row"
                                :key="sellerItem.seller.id"
                            >

                                <td data-label="Vendedor:">

                                    @{{ sellerItem.seller.name }}
                                    @{{ sellerItem.seller.last_name }}

                                </td>

                                <td data-label="Cantidad de asignaciones:">

                                    @{{ sellerItem.count }}

                                </td>

                                <td
                                    class="table-resource__actions"
                                    data-label="Acciones:"
                                >

                                    <a
                                        class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                        :href="$root.path + '/admin/inventario-vendedores/' + sellerItem.seller.id + '/detalle'"
                                    >

                                        <img
                                            class="svg-icon"
                                            src="{{ url('img/svg/edit.svg') }}"
                                        >

                                        Detalles

                                    </a>

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