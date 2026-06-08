@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'ventas')
@section('tab_title', 'ventas | ' . config('app.name'))
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
        
        
        <tabs-component :tabs="{{ json_encode($statusLabels) }}" initial="quoted">
            @foreach ($statusLabels as $status => $label)
                <template #panel-{{ $status }}>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Lista de {{ $label }}
                        </h3>
                        

                        @if ($salesByStatus[$status]['items']->isEmpty())
                            <p class="text-center py-1">
                                No hay elementos en esta pestaña.
                            </p>
                        @else
                        <resource-table :breakpoint="800" :model="{{ json_encode($salesByStatus[$status]['items']) }}" inline-template>
                            <table class="table size-caption mx-auto mb-16 md:table--responsive">
                                <thead>
                                    <tr class="table-resource__headings">
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Subtotal</th>
                                        <th>IVA</th>
                                        <th>Total</th>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isEmployee())
                                        <th>Orden</th>
                                        @endif
                                        <th>Nota</th>
                                        <th class="pr-2">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="salesItem in resourceList" class="table-resource__row" :key="salesItem.id">
                                        <td data-label="Fecha:">
                                            @{{ salesItem.id }}
                                        </td>
                                        <td data-label="Fecha:">
                                            @{{ salesItem.formated_date }}
                                        </td>
                                        <td data-label="Hora:">
                                            @{{ salesItem.hour }}
                                        </td>
                                        <td data-label="Cliente:">
                                            @{{ salesItem.user.name }} @{{ salesItem.user.last_name }}
                                        </td>
                                        <td data-label="Subtotal:">
                                            $@{{ salesItem.total_sale_price }}
                                        </td>
                                        <td data-label="IVA:">
                                            $@{{ salesItem.iva }}
                                        </td>
                                        <td data-label="Total:">
                                            $@{{ salesItem.total_with_iva }}
                                        </td>
                                                           
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isEmployee())
                                            <td data-label="Orden de trabajo:">
                                                <a class="btn btn-nowrap btn--sm btn--blue table-resource__button" :href="$root.path + '/admin/ventas/orden/' + salesItem.id">
                                                    <img class="svg-icon-only" src="{{ url('img/svg/order.svg')}}">
                                                </a>
                                            </td>
                                        @endif
                                        <td data-label="Nota:">
                                            <a class="btn btn-nowrap btn--sm btn--blue table-resource__button" :href="$root.path + '/notas/' + salesItem.id" target="_blank">
                                                <img class="svg-icon-only" src="{{ url('img/svg/pdf.svg')}}">
                                            </a>
                                        </td>
                                        <td class="table-resource__actions" data-label="Acciones:">
                                            @if($status == 'quoted')
                                                <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/ventas/' + salesItem.id + '/editar' ">
                                                    <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                                    Editar
                                                </a>
                                                
                                                <clone-button 
                                                    class="btn btn-nowrap btn--sm btn--success table-resource__button mr-2" 
                                                    :item="salesItem" 
                                                    :url="$root.path + '/admin/ventas/clonar/' + salesItem.id" 
                                                    @clone="cloneResource"
                                                >
                                                    <img class="svg-icon" src="{{ url('img/svg/clone.svg')}}">
                                                    Clonar
                                                </clone-button>
                                                <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/ventas/eliminar/' + salesItem.id"
                                                    :resource-id="salesItem.id"
                                                    :options="{ onDelete: onResourceDelete }"
                                                >
                                                    <img class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                                    Eliminar
                                                </delete-button>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                </tbody>

                            </table>

                        </resource-table>

                        {!! $salesByStatus[$status]['links'] !!}
                            

                    @endif

                </section>
            </template>
        @endforeach
    </tabs-component> 
</div>
@endsection
