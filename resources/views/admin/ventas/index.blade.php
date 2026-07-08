@extends('layout.dashboard-master')

@section('title', 'Ventas')
@section('tab_title', 'Ventas | ' . config('app.name'))
@section('description', 'Lista de ventas.')
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">
    <div class="md:row justify-between">
        <div class="md:col-1/2">
            <h1 class="dashboard-heading__title">
                Ventas
            </h1>
        </div>

        <div class="md:col-1/2 d-flex items-center">
            <div class="row">
                <div class="md:col-1/3">
                    <label>Día</label>
                    <select-filter
                        name="day"
                        selected="{{ request('day', $actual_day) }}"
                        :options="{{ $days }}"
                    >
                    </select-filter>
                </div>
                <div class="md:col-1/3">
                    <label>Mes</label>
                    <select-filter
                        name="month"
                        selected="{{ request('month', $actual_month) }}"
                        :options="{{ $months }}"
                    >
                    </select-filter>
                </div>
                <div class="md:col-1/3">
                    <label>Año</label>
                    <select-filter
                        name="year"
                        selected="{{ request('year', $actual_year) }}"
                        :options="{{ $years }}"
                    >
                    </select-filter>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fluid-container mb-16">

    @include('components.alert')

    <form-search
        selected="{{ request('search') }}"
    >
        <template slot="svg-search">
            <img class="search-form_icon" src="{{ url('img/svg/search.svg') }}">
        </template>
    </form-search>

    <section class="db-panel">

        <h3 class="db-panel__title">
            Lista de ventas
        </h3>

        @if($saleItems->isEmpty())

            <p class="text-center py-1">
                No hay ventas registradas.
            </p>

        @else

            <resource-table
                :breakpoint="800"
                :model="{{ json_encode($saleItems) }}"
                inline-template
            >

                <table class="table size-caption mx-auto mb-16 md:table--responsive">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Total</th>

                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isEmployee())
                                <th>Orden</th>
                            @endif

                            <th>Nota</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr
                            v-for="saleItem in resourceList"
                            :key="saleItem.id"
                        >

                            <td>@{{ saleItem.id }}</td>

                            <td>@{{ saleItem.formated_date }}</td>

                            <td>@{{ saleItem.hour }}</td>

                            <td>
                                @{{ saleItem.user.name }}
                                @{{ saleItem.user.last_name }}
                            </td>

                            <td>

                                <span
                                    v-if="saleItem.status=='paid'"
                                    class="badge badge--success"
                                >
                                    Pagada
                                </span>

                                <span
                                    v-else-if="saleItem.status=='credit'"
                                    class="badge badge--warning"
                                >
                                    Crédito
                                </span>

                                <span
                                    v-else-if="saleItem.status=='assortment'"
                                    class="badge badge--warning"
                                >
                                    Surtida
                                </span>

                                <span
                                    v-else-if="saleItem.status=='accepted'"
                                    class="badge badge--warning"
                                >
                                    Aceptada
                                </span>

                            </td>

                            <td>
                                $@{{ saleItem.gross_amount }}
                            </td>

                            <td>
                                $@{{ saleItem.discount }}
                            </td>

                            <td>
                                $@{{ saleItem.total_with_iva }}
                            </td>

                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isEmployee())

                                <td>

                                    <a
                                        class="btn btn--sm btn--blue"
                                        :href="$root.path + '/admin/ventas/orden/' + saleItem.id"
                                    >
                                        <img class="svg-icon-only" src="{{ url('img/svg/order.svg') }}">
                                    </a>

                                </td>

                            @endif

                            <td>

                                <a
                                    class="btn btn--sm btn--blue"
                                    :href="$root.path + '/notas/' + saleItem.id"
                                    target="_blank"
                                >
                                    <img class="svg-icon-only" src="{{ url('img/svg/pdf.svg') }}">
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