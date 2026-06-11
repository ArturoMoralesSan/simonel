@extends('layout.dashboard-master')

@section('meta.title', 'Panel de administración')
@section('meta.tab_title', 'Panel de administración | ' . config('app.name'))
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">
    <div class="md:row justify-between items-center">
        <div class="md:col-1/2">
            <h1 class="dashboard-heading__title">
                Panel de administración
            </h1>
        </div>

        <div class="md:col-1/2 d-flex items-center justify-end">
            <form-between-date-search
                selectedstart="{{ request('start_date') }}"
                selectedend="{{ request('end_date') }}"
            >
                <template slot="svg-search">
                    <img
                        class="search-form_icon--55"
                        src="{{ url('img/svg/search.svg') }}"
                        alt=""
                    >
                </template>
            </form-between-date-search>
        </div>
    </div>
</div>

<div class="fluid-container mb-16">

    @include('components.alert')

    {{-- RESUMEN DEL DÍA --}}
    <section class="db-panel">
        <div class="row">

            <div class="column-statistics">
                <strong>{{ $string_date }}</strong>
                <br>
                Hoy
            </div>

            <div class="column-statistics">
                <strong>${{ $ingresoNow }}</strong>
                <br>
                Ventas de hoy
            </div>

            <div class="column-statistics">
                <strong>{{ $salesNowCount }}</strong>
                <br>
                Órdenes hoy
            </div>

        </div>
    </section>

    {{-- KPI PRINCIPALES --}}
    <section class="db-panel">
        <div class="row">

            <div class="column-statistics">
                <strong>${{ $ingreso }}</strong>
                <br>
                Ingresos
            </div>

            <div class="column-statistics">
                <strong>{{ $salesCount }}</strong>
                <br>
                Ventas
            </div>

            <div class="column-statistics">
                <strong>${{ number_format($averageTicket ?? 0, 2) }}</strong>
                <br>
                Ticket promedio
            </div>

            <div class="column-statistics">
                <strong>{{ $newCustomers ?? 0 }}</strong>
                <br>
                Clientes nuevos
            </div>

        </div>
    </section>

    {{-- GRAFICA --}}
    <section class="db-panel">
        <h3 class="db-panel__title">
            Evolución de ingresos
        </h3>

        <canvas id="canvas" class="graph-statistics-income"></canvas>
    </section>

    {{-- SEGUNDO BLOQUE DE KPIS --}}
    <section class="db-panel">
        <div class="row">

            <div class="column-statistics">
                <strong>{{ number_format($totalKgSold ?? 0, 2) }}</strong>
                <br>
                Kg vendidos
            </div>

            <div class="column-statistics">
                <strong>{{ $paidSales ?? 0 }}</strong>
                <br>
                Ventas pagadas
            </div>

            <div class="column-statistics">
                <strong>{{ $pendingSales ?? 0 }}</strong>
                <br>
                Pendientes
            </div>

            <div class="column-statistics">
                <strong>{{ $topProductName ?? 'N/D' }}</strong>
                <br>
                Producto líder
            </div>

        </div>
    </section>

    <div class="md:row">

        {{-- PRODUCTOS --}}
        <div class="md:col-1/2">
            <section class="db-panel">

                <h3 class="db-panel__title">
                    Productos más vendidos
                </h3>

                <resource-table
                    :breakpoint="800"
                    :model="{{ $productsCount }}"
                    inline-template
                >

                    <table class="table size-caption mx-auto md:table--responsive">

                        <thead>
                            <tr class="table-resource__headings">
                                <th>Producto</th>
                                <th>Ventas</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="productItem in resourceList"
                                :key="productItem.id"
                                class="table-resource__row"
                            >
                                <td data-label="Producto">
                                    @{{ productItem.name }}
                                </td>

                                <td data-label="Ventas">
                                    @{{ productItem.sales_count }}
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </resource-table>

            </section>
        </div>

        {{-- CLIENTES --}}
        <div class="md:col-1/2">
            <section class="db-panel">

                <h3 class="db-panel__title">
                    Mejores clientes
                </h3>

                <resource-table
                    :breakpoint="800"
                    :model="{{ $customerCount }}"
                    inline-template
                >

                    <table class="table size-caption mx-auto md:table--responsive">

                        <thead>
                            <tr class="table-resource__headings">
                                <th>Cliente</th>
                                <th>Compras</th>
                                <th>Monto</th>
                                <th>PDF</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="customerItem in resourceList"
                                :key="customerItem.id"
                                class="table-resource__row"
                            >
                                <td data-label="Cliente">
                                    @{{ customerItem.name }}
                                </td>

                                <td data-label="Compras">
                                    @{{ customerItem.sales_count }}
                                </td>

                                <td data-label="Monto">
                                    $
                                    @{{ Number(customerItem.total_sale_price || 0).toFixed(2) }}
                                </td>

                                <td data-label="Reporte">

                                    <link-pdf
                                        :branchid="customerItem.id"
                                        url="/admin/pdf-cliente/"
                                        startdate="{{ request('start_date') }}"
                                        enddate="{{ request('end_date') }}"
                                    >
                                    </link-pdf>

                                </td>
                            </tr>

                        </tbody>

                    </table>

                </resource-table>

            </section>
        </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.js"></script>

<script>

    const amountPerDay = {!! $days !!};

    const lineChartData = {

        labels: Object.keys(amountPerDay),

        datasets: [{
            label: 'Ingresos',
            fill: true,
            backgroundColor: 'rgba(64,157,205,0.20)',
            borderColor: '#36A2EB',
            tension: 0.4,
            data: Object.values(amountPerDay)
        }]
    };

    window.onload = function () {

        const ctx = document
            .getElementById('canvas')
            .getContext('2d');

        new Chart(ctx, {

            type: 'line',

            data: lineChartData,

            options: {

                responsive: true,

                plugins: {
                    legend: {
                        display: true
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    };

</script>

@endsection