@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Proveedores')
@section('tab_title', 'Proveedores | ' . config('app.name'))
@section('description', 'Proveedores.')
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Proveedores
    </h1>

    <p class="dashboard-heading__caption">
        Hay {{ $supplierItems->count() }} proveedores registrados.
    </p>
</div>

<div class="fluid-container mb-16">

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

    @include('components.alert')

    <section class="db-panel">
        <h3 class="db-panel__title">
            Lista de proveedores
        </h3>

        @if (!$supplierItems->count())

            <p class="text-center py-1">
                Por el momento no hay proveedores registrados.
            </p>

        @else

            <resource-table
                :breakpoint="800"
                :model="{{ $supplierItems }}"
                inline-template
            >

                <table class="table size-caption mx-auto mb-16 md:table--responsive">

                    <thead>
                        <tr class="table-resource__headings">
                            <th>Razón social</th>
                            <th>Nombre comercial</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>RFC</th>
                            <th class="pr-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="supplier in resourceList"
                            :key="supplier.id"
                            class="table-resource__row"
                        >

                            <td data-label="Razón social:">
                                @{{ supplier.business_name }}
                            </td>

                            <td data-label="Nombre comercial:">
                                @{{ supplier.trade_name || '-' }}
                            </td>

                            <td data-label="Contacto:">
                                @{{ supplier.contact_name || '-' }}
                            </td>

                            <td data-label="Teléfono:">
                                @{{ supplier.phone || '-' }}
                            </td>

                            <td data-label="RFC:">
                                @{{ supplier.rfc || '-' }}
                            </td>

                            <td
                                class="table-resource__actions"
                                data-label="Acciones:"
                            >

                                <a
                                    class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                    :href="$root.path + '/admin/proveedores/' + supplier.id + '/editar'"
                                >
                                    <img
                                        class="svg-icon"
                                        src="{{ url('img/svg/edit.svg') }}"
                                    >
                                    Editar
                                </a>
                                <delete-button
                                    v-if="supplier.purchases_count == 0"
                                    class="btn--danger table-resource__button"
                                    :url="$root.path + '/admin/proveedores/eliminar/' + supplier.id"
                                    :resource-id="supplier.id"
                                    :options="{ onDelete: onResourceDelete }"
                                >
                                    <img
                                        class="svg-icon"
                                        src="{{ url('img/svg/trash.svg') }}"
                                    >
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