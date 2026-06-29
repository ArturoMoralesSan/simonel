@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Compras')
@section('tab_title', 'Compras | ' . config('app.name'))
@section('description', 'Compras de materia prima.')
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Compras
    </h1>

    <p class="dashboard-heading__caption">
        Hay {{ $purchaseItems->count() }} compras registradas.
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

        <div class="flex justify-between items-center mb-4">
            <h3 class="db-panel__title">
                Lista de compras
            </h3>

        </div>

        @if (!$purchaseItems->count())

            <p class="text-center py-1">
                Por el momento no hay compras registradas.
            </p>

        @else

            <resource-table
                :breakpoint="800"
                :model="{{ $purchaseItems }}"
                inline-template
            >

                <table class="table size-caption mx-auto mb-16 md:table--responsive">

                    <thead>
                        <tr class="table-resource__headings">
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Factura</th>
                            <th>Total</th>
                            <th>Materias primas</th>
                            <th class="pr-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr
                            v-for="purchase in resourceList"
                            :key="purchase.id"
                            class="table-resource__row"
                        >
                            <td data-label="Fecha:">
                                @{{ purchase.purchase_date_formatted }}
                            </td>

                            <td data-label="Proveedor:">
                                @{{ purchase.supplier.business_name }}
                            </td>

                            <td data-label="Factura:">
                                @{{ purchase.invoice_number || 'Sin factura' }}
                            </td>

                            <td data-label="Total:">
                                $ @{{ purchase.total }}
                            </td>

                            <td data-label="Materias primas:">
                                @{{ purchase.items_count }}
                            </td>

                            <td
                                class="table-resource__actions"
                                data-label="Acciones:"
                            >

                                <a
                                    class="btn btn-nowrap btn--sm btn--primary table-resource__button mr-2"
                                    :href="$root.path + '/admin/compras/' + purchase.id + '/detalle'"
                                >
                                    Ver
                                </a>
                                <a
                                    class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                    :href="$root.path + '/admin/compras/' + purchase.id + '/editar'"
                                >
                                    <img
                                        class="svg-icon"
                                        src="{{ url('img/svg/edit.svg') }}"
                                    >
                                    Editar
                                </a>

                                

                                <delete-button
                                    class="btn--danger table-resource__button"
                                    :url="$root.path + '/admin/compras/' + purchase.id"
                                    :resource-id="purchase.id"
                                    :options="{ onDelete: onResourceDelete }"
                                    :disabled="purchase.lot_count != 0"
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