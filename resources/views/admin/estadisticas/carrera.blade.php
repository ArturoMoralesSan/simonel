@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('meta.title', 'Estadística carrera' )
@section('meta.tab_title', 'Estadística carrera | ' . config('app.name'))
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <div class="md:row justify-between">
            <div class="md:col-1/2">
                <h1 class="dashboard-heading__title">
                    Estadística carrera
                </h1>
            </div>
        </div>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        
        
        <section class="db-panel">
            <div class="row">
                <div class="column-statistics-1-3 ">
                    <strong>${{ number_format($amountByRegisters, 2, '.') }}</strong> <br>
                    Ingresos
                </div>
                <div class="column-statistics-1-3 mb-0">
                    <strong> {{ $ordersAll }}</strong> <br>
                    Registros
                </div>
            </div>
        </section>
        <div class="md:row">
            <div class="md:col-1/3">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Registro por tipo de carrera
                    </h3>
                    <canvas id="canvastypes" class="graph-statistics-pay"></canvas>
                </section>
            </div>

            <div class="md:col-1/3">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Registro por talla
                    </h3>
                    <canvas id="canvassize" class="graph-statistics-pay"></canvas>
                </section>
            </div>
            <div class="md:col-1/3">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Registro por sexo
                    </h3>
                    <canvas id="canvassex" class="graph-statistics-pay"></canvas>
                </section>
            </div>
            
        </div>
        
        <div class="md:row">
            <div class="md:col">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Total de registrados por sucursal
                    </h3>
                    <resource-table :breakpoint="800" :model="{{ $registersall }}" inline-template>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Sucursal</th>
                                    <th>cantidad</th>
                                    <th>Monto</th>
                                    <th>Reporte</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="registerItem in resourceList" class="table-resource__row" :key="registerItem.id">
                                    <td data-label="Estudio:">
                                        @{{ registerItem.branch_name }}
                                    </td>
                                    <td data-label="Cantidad:">
                                        @{{ registerItem.total_registers }}
                                    </td>
                                    <td data-label="Monto:">
                                        $@{{ registerItem.total_amount }}
                                    </td>
                                    <td data-label="Reporte:">
                                        <link-pdf 
                                            :branchid="registerItem.id" 
                                            url="/admin/pdf-carrera/"
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
        var totalPerType = <?php echo $totalPerType; ?>;
        var totalPerSize = <?php echo $totalPerSize; ?>;
        var totalPerSex = <?php echo $totalPerSex; ?>;

        var barChartDataTypes = {
            labels: Object.keys(totalPerType),
            datasets: [{
                label: 'Total',
                backgroundColor: ['#9BD0F5'],
                borderColor: '#36A2EB',
                data: Object.values(totalPerType)
            }]
        };

        var barChartDataSize = {
            labels: Object.keys(totalPerSize),
            datasets: [{
                label: 'Total',
                backgroundColor: ['#9BD0F5'],
                borderColor: '#36A2EB',
                data: Object.values(totalPerSize)
            }]
        };

        var barChartDataSex = {
            labels: Object.keys(totalPerSex),
            datasets: [{
                label: 'Total',
                backgroundColor: ['#9BD0F5'],
                borderColor: '#36A2EB',
                data: Object.values(totalPerSex)
            }]
        };

        window.onload = function() {
            
            var ctxb = document.getElementById("canvastypes").getContext("2d");
            window.myLineb = new Chart(ctxb, {
                type: 'bar',
                data: barChartDataTypes,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de registrados por tipo de carrera'
                    }
                }
            });

            var ctxb = document.getElementById("canvassize").getContext("2d");
            window.myLineb = new Chart(ctxb, {
                type: 'bar',
                data: barChartDataSize,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de registrados por talla'
                    }
                }
            });

            var ctxb = document.getElementById("canvassex").getContext("2d");
            window.myLineb = new Chart(ctxb, {
                type: 'bar',
                data: barChartDataSex,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de registrados por sexo'
                    }
                }
            });
        };
    </script>

@endsection
