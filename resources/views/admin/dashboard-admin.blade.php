@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('meta.title', 'Panel de administración' )
@section('meta.tab_title', 'Panel de administración | ' . config('app.name'))
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <div class="md:row justify-between">
            <div class="md:col-1/2">
                <h1 class="dashboard-heading__title">
                    Panel de administración
                </h1>
            </div>
            <div class="md:col-1/2 d-flex items-center">
                <form-between-date-search
                        selectedstart="{{ app('request')->input('start_date') }}"
                        selectedend="{{ app('request')->input('end_date') }}"
                    >
                    <template slot="svg-search">
                        <img class="search-form_icon--55" src="{{ url('img/svg/search.svg') }}" alt="">
                    </template>
                </form-between-date-search>
            </div>
        </div>
        
        <div>
            
        </div>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <div class="row">
                <div class="column-statistics">
                   <strong>
                    Hoy <br>
                    {{ $string_date }}
                   </strong> 
                </div>
                <div class="column-statistics">
                    <strong>${{ $ingresoNow }}</strong> <br>
                    Ingresos
                </div>
                
                <div class="column-statistics mb-0">
                    <strong>{{ $salesNowCount }}</strong> <br>
                    Ventas
                </div>
            </div>
        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Ingresos
            </h3>
            <canvas id="canvas" class="graph-statistics-income"></canvas>
        </section>
        <section class="db-panel">
            <div class="row">
                <div class="column-statistics-1-3 ">
                    <strong>${{ $ingreso}}</strong> <br>
                    Ingresos
                </div>
                <div class="column-statistics-1-3 mb-0">
                    <strong> {{ $salesCount }}</strong> <br>
                    Ventas
                </div>
            </div>
        </section>
        <div class="md:row">
            <div class="md:col-1/2">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Productos más populares
                    </h3>
                    <resource-table :breakpoint="800" :model="{{ $productsCount }}" inline-template>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="productItem in resourceList" class="table-resource__row" :key="productItem.id">
                                    <td data-label="Estudio:">
                                        @{{ productItem.name }}
                                    </td>
                                    <td data-label="Cantidad:">
                                        @{{ productItem.sales_count }}
                                    </td>
                                    
                                </tr>
                            </tbody>
                        </table>

                    </resource-table>
                </section>
            </div>
            <div class="md:col-1/2">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Mejores clientes
                    </h3>
                    <resource-table :breakpoint="800" :model="{{ $customerCount }}" inline-template>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Nombre</th>
                                    <th>Cantidad de compras</th>
                                    <th>Monto</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="customerItem in resourceList" class="table-resource__row" :key="customerItem.id">
                                    <td data-label="Gasto:">
                                        
                                        @{{ customerItem.name }}
                                    </td>
                                    <td data-label="Cantidad:">
                                        @{{ customerItem.sales_count }}
                                    </td>
                                    <td data-label="Cantidad:">
                                        $ @{{ customerItem.total_sale_price }}
                                    </td>
                                    <td data-label="Reporte:">
                                    <link-pdf 
                                            :branchid="customerItem.id" 
                                            url="/admin/pdf-cliente/"
                                            startdate="{{ app('request')->input('start_date') }}"
                                            enddate="{{ app('request')->input('end_date') }}">
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

    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.js"></script>
    <script type="application/javascript">
        var amountPerDay = <?php echo $days; ?>;

        var lineChartData = {
            labels: Object.keys(amountPerDay),
            datasets: [{
                label: 'Total',
                fill: true,
                backgroundColor: ['rgba(64, 157, 205, 0.3)'],
                borderColor: '#36A2EB',
                data: Object.values(amountPerDay)
            }]
        };

        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                options: {
                    responsive: true,                    title: {
                        display: true,
                        text: 'Total de Ingresos'
                    }
                }
            });
        };
    </script>

@endsection
