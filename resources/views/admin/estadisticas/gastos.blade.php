@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Estadísticas de gastos')
@section('tab_title', 'Estadísticas de gastos | ' . config('app.name'))
@section('description', 'Lista de Estadísticas de gastos.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Estadísticas de gastos
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <section class="db-panel">
            <h3 class="db-panel__title">
                Filtro
            </h3>
            <div class="md:row">
                <div class="md:col-1/4">
                    <label for="date">Fecha</label>
                    <form-date-search
                        name="date"
                        selected="{{ app('request')->input('date') }}"
                    >
                        <template slot="svg-search">
                            <img class="search-form_icon" src="{{ url('img/svg/search.svg') }}" alt="">
                        </template>
                    </form-date-search>
                </div>
                <div class="md:col-1/4">
                    <label for="month">Mes</label>
                    <select-filter
                        name="month"
                        selected="{{ app('request')->input('month') }}"
                        :options="{{ $months }}"
                    >
                    </select-filter>
                </div>
                <div class="md:col-1/4">
                    <label for="year">Año</label>
                    <select-filter
                        name="year"
                        selected="{{ app('request')->input('year') }}"
                        :options="{{ $years }}"
                    >
                    </select-filter>
                </div>
                <div class="md:col-1/4">
                    <label for="branch">Sucursales</label>
                    <select-filter
                        name="branch"
                        selected="{{ app('request')->input('branch') }}"
                        :options="{{ $branches }}"
                    >
                    </select-filter>
                </div>
            </div>
        </section>
        
        <div class="md:row">
            <div class="md:col-1/2">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Estadísticas de gastos totales
                    </h3>
                    <canvas id="canvas" height="400"></canvas>
                </section>
            </div>
            <div class="md:col-1/2">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Estadísticas de gastos por sucursal
                    </h3>
                    <canvas id="canvasbybranch" height="400"></canvas>
                </section>
            </div>
        </div>
        <div class="md:row">
        <div class="md:col-2/3">
            <section class="db-panel">
                <h3 class="db-panel__title">
                    Estadísticas de gastos por tipo de gasto
                </h3>
                <canvas id="canvasbytype" height="400"></canvas>
            </section>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.js"></script>
    <script type="application/javascript">
        var amount = <?php echo $expenses_by_amount; ?>;
        var amountbyBranch = <?php echo $expenses_branch_by_amount; ?>;
        var amountbytype = <?php echo $expenses_type_by_amount; ?>;

        var barChartDataTotal = {
            labels: ['Total'],
            datasets: [{
                label: 'Total',
                backgroundColor: ["#409dcd"],
                data: amount
            }]
        };
        var barChartDataBranch = {
            labels: Object.keys(amountbyBranch),
            datasets: [{
                label: 'Total',
                backgroundColor: ["#409dcd","#456c7f","53c0f5"],
                data: Object.values(amountbyBranch)
            }]
        };
        var barChartDatapayment = {
            labels: Object.keys(amountbytype),
            datasets: [{
                label: 'Total',
                backgroundColor: ["#409dcd","#456c7f","53c0f5"],
                data: Object.values(amountbytype)
            }]
        };

        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartDataTotal,
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: '#409dcd',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de gastos'
                    }
                }
            });
           var ctxb = document.getElementById("canvasbybranch").getContext("2d");
            window.myBarb = new Chart(ctxb, {
                type: 'bar',
                data: barChartDataBranch,
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: '#409dcd',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de gastos por sucursal'
                    }
                }
            });
            var ctxp = document.getElementById("canvasbytype").getContext("2d");
            window.myPiep = new Chart(ctxp, {
                type: 'bar',
                data: barChartDatapayment,
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Total de por tipo de gasto'
                    }
                }
            });
        };
    </script>
    
   
@endsection
