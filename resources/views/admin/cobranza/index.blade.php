@extends('layout.dashboard-master')

@section('title', 'Cobranza')
@section('tab_title', 'Cobranza | ' . config('app.name'))
@section('description', 'Cuentas por cobrar')
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">

    <h1 class="dashboard-heading__title">
        Cobranza
    </h1>

    <p class="dashboard-heading__caption">
        Hay {{ $accountsItems->count() }} cuentas por cobrar.
    </p>

</div>

<div class="fluid-container mb-16">

    @include('components.alert')

    {{-- KPIs --}}
    <div class="row mb-6">

        <div class="md:col-1/4">

            <div class="db-panel text-center">

                <h4>Saldo pendiente</h4>

                <h2>
                    ${{ number_format($summary['pending'],2) }}
                </h2>

            </div>

        </div>

        <div class="md:col-1/4">

            <div class="db-panel text-center">

                <h4>Saldo vencido</h4>

                <h2 class="color-danger">
                    ${{ number_format($summary['overdue'],2) }}
                </h2>

            </div>

        </div>

        <div class="md:col-1/4">

            <div class="db-panel text-center">

                <h4>Clientes con adeudo</h4>

                <h2>
                    {{ $summary['customers'] }}
                </h2>

            </div>

        </div>

        <div class="md:col-1/4">

            <div class="db-panel text-center">

                <h4>Cuentas abiertas</h4>

                <h2>
                    {{ $summary['documents'] }}
                </h2>

            </div>

        </div>

    </div>

    <form-search
        selected="{{ request('customer') }}">
        <template slot="svg-search">
            <img
                class="search-form_icon"
                src="{{ url('img/svg/search.svg') }}"
            >
        </template>
    </form-search>

    <section class="db-panel">

        <h3 class="db-panel__title">
            Cuentas por cobrar
        </h3>

        @if(!$accountsItems->count())

            <p class="text-center py-8">
                No existen cuentas por cobrar.
            </p>

        @else

            <resource-table
                :breakpoint="900"
                :model="{{ $accountsItems }}"
                inline-template>

                <table class="table size-caption md:table--responsive">

                    <thead>

                        <tr>

                            <th></th>
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vence</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="account in resourceList"
                            :key="account.id">

                            <td data-label="">

                                <span
                                    v-if="account.status=='Pagada'">
                                    🟢
                                </span>

                                <span
                                    v-else-if="new Date(account.due_date) < new Date()">
                                    🔴
                                </span>

                                <span
                                    v-else>
                                    🟡
                                </span>

                            </td>

                            <td data-label="Venta">

                                #@{{ account.sale_id }}

                            </td>

                            <td data-label="Cliente">

                                @{{ account.customer.business_name }}

                            </td>

                            <td data-label="Fecha">

                               @{{ account.formated_issue_date }}

                            </td>

                            <td data-label="Vence">

                                @{{ account.formated_due_date }}

                            </td>

                            <td data-label="Total">
                                $@{{ Number(account.original_amount || 0).toLocaleString('es-MX', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) }}

                            </td>

                            <td data-label="Pagado">

                                $@{{ Number(account.paid_amount || 0).toLocaleString('es-MX', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) }}

                            </td>

                            <td data-label="Saldo">

                                $@{{ Number(account.balance || 0).toLocaleString('es-MX', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) }}

                            </td>

                            <td data-label="Estado">

                                @{{ account.status }}

                            </td>

                            <td
                                class="table-resource__actions"
                                data-label="Acciones">

                                <a
                                    class="btn btn--sm btn--blue mr-2"
                                    :href="$root.path+'/admin/cuentas-pendientes/'+account.id">
                                    Ver
                                </a>
                                <a
                                    class="btn btn--sm btn--blue mr-2"
                                    :href="$root.path+'/admin/cuentas-pendientes/'+account.id+'/pago'">
                                    Pagar
                                </a>
                            </td>
                        </tr>
                    </tbody>

                </table>

            </resource-table>

        @endif

    </section>

</div>

@endsection