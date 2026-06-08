@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'producción')
@section('tab_title', 'producción | ' . config('app.name'))
@section('description', 'Lista de producción.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <div class="md:row justify-between">
            <div class="md:col-1/2">
                <h1 class="dashboard-heading__title">
                    Ordenes de producción
                </h1>
                
            </div>
            <div class="md:col-1/2 d-flex items-center">
                <div class="row">
                    <div class="md:col-1/2">
                        <label for="month">Mes</label>
                        <select-filter
                            name="month"
                            selected="{{ app('request')->input('month') ? app('request')->input('month') : $actual_month }}"
                            :options="{{ $months }}"
                        >
                        </select-filter>
                    </div>
                    <div class="md:col-1/2">
                        <label for="year">Año</label>
                        <select-filter
                            name="year"
                            selected="{{ app('request')->input('year') ? app('request')->input('year'): $actual_year }}"
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
            selected="{{ app('request')->input('search') }}"
        >
        <template slot="svg-search">
            <img class="search-form_icon" src="{{ url('img/svg/search.svg') }}" alt="">
        </template>
        </form-search>
        
        
        <tabs-component :tabs="{{ json_encode($statusLabels) }}" initial="creada">
            @foreach ($statusLabels as $status => $label)
                <template #panel-{{ $status }}>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Lista de {{ $label }}
                        </h3>
                    
                        @if ($ordersByStatus[$status]['items']->isEmpty())
                            <p class="text-center py-1">
                                No hay elementos en esta pestaña.
                            </p>
                        @else
                            <resource-table :breakpoint="800" :model="{{ json_encode($ordersByStatus[$status]['items']) }}" inline-template>
                                <table class="table size-caption mx-auto mb-16 md:table--responsive">
                                    <thead>
                                        <tr class="table-resource__headings">
                                            <th>Orden</th>
                                            <th>Productos</th>
                                            <th>Total Producción</th>
                                            <th>Fecha emisión</th>
                                            <th>Fecha entrega</th>
                                            <th>Costo estimado</th>
                                            <th>Autorizó</th>
                                            <th class="pr-2">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr
                                            v-for="orderItem in resourceList"
                                            class="table-resource__row"
                                            :key="orderItem.id"
                                        >
                                            <td data-label="Orden:">
                                                @{{ orderItem.order_number }}
                                            </td>

                                            <td
                                                data-label="Productos:"
                                                v-html="orderItem.products_list"
                                            >
                                            </td>

                                            <td data-label="Total Producción:">
                                                @{{ orderItem.total_quantity }}
                                            </td>

                                            <td data-label="Fecha emisión:">
                                                @{{ orderItem.formated_issue_date }}
                                            </td>

                                            <td data-label="Fecha entrega:">
                                                @{{ orderItem.formated_delivery_date }}
                                            </td>

                                            <td data-label="Costo estimado:">
                                                $ @{{ orderItem.estimated_cost }}
                                            </td>

                                            <td data-label="Autorizó:">
                                                @{{
                                                    orderItem.authorizer
                                                    ? orderItem.authorizer.name + ' ' + orderItem.authorizer.last_name
                                                    : '-'
                                                }}
                                            </td>

                                            <td
                                                class="table-resource__actions"
                                                data-label="Acciones:"
                                            >

                                                <a
                                                    class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                                    :href="$root.path + '/admin/produccion/' + orderItem.id + '/detalle'"
                                                >
                                                    <img
                                                        class="svg-icon-only"
                                                        src="{{ url('img/svg/order.svg')}}"
                                                    >
                                                </a>

                                                <a
                                                    class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                                    :href="$root.path + '/admin/produccion/' + orderItem.id + '/editar'"
                                                >
                                                    <img
                                                        class="svg-icon"
                                                        src="{{ url('img/svg/edit.svg')}}"
                                                    >
                                                    Editar
                                                </a>

                                                <delete-button
                                                    class="btn--danger table-resource__button"
                                                    :url="$root.path + '/admin/produccion/eliminar/' + orderItem.id"
                                                    :resource-id="orderItem.id"
                                                    :options="{ onDelete: onResourceDelete }"
                                                >
                                                    <img
                                                        class="svg-icon"
                                                        src="{{ url('img/svg/trash.svg')}}"
                                                    >
                                                    Eliminar
                                                </delete-button>

                                            </td>
                                        </tr>
                                    </tbody>

                                </table>

                            </resource-table>

                            {!! $ordersByStatus[$status]['links'] !!}
                                
                            @endif

                    </section>
                </template>
            @endforeach
        </tabs-component> 
    </div>
@endsection
