<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="page_break mt-4">
        <img style="width: 160px;display: block;margin: 0 auto;" src="{{ url('img/imax-logo.png')}}" alt="">
        <div class="row">
            <div class="col-md-12">
                <h6>Reporte del periodo ({{ $start_date }} al {{ $end_date }})</h6>
                <table class="table table-bordered">
                    <caption>Lista de Servicios</caption>
                    <thead>
                      <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Hora</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Estudios</th>
                        <th scope="col">costo</th>
                        <th scope="col">Impresa</th>
                        <th scope="col">N° RX</th>
                        

                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                        <tr>
                            <th scope="row">{{ $service->id }}</th>
                            <td>{{ $service->hour }}</td>
                            <td>{{ $service->patient }}</td>
                            <td>{{ $service->list_studies }}</td>
                            <td>${{ number_format($service->cost, 2, ".") }}</td>
                            <td>{{ $service->print }}</td>
                            <td>{{ $service->no_rx }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            
                            <td colspan="4">Total</td>
                            <th>${{ number_format($services->sum('cost'), 2, ".") }}</th>
                            <th>-</th>
                            <th>{{ $services->sum('no_rx')}}</th>
                        </tr>
                    </tfoot>
                  </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <table class="table table-bordered">
                    <caption>Lista de gastos</caption>
                    <thead>
                      <tr>
                        <th scope="col">Gasto</th>
                        <th scope="col">Monto</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                        <tr>
                            <th scope="row">
                                @if($expense->type_expense->key_name == 'comisiones')
                                    {{ $expense->type_expense->name }} ({{ $expense->person_name }})
                                @else
                                    {{ $expense->type_expense->name }}
                                @endif
                            </th>
                            <td>
                                ${{ number_format($expense->amount, 2, ".") }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <th>${{ number_format($expenses->sum('amount'), 2, ".") }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">Total - Gasto</th>
                      </tr>
                    </thead>
                    <tbody>
                        <td>
                            ${{ number_format($services->sum('cost') -  $expenses->sum('amount'), 2, ".") }}
                        </td>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <table class="table table-bordered">
                <caption>Lista de ingresos por método de pago</caption>
                <thead>
                    <tr>
                    <th scope="col">Tipo de pago</th>
                    <th scope="col">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicesPerPayments as $key => $payment)
                    <tr>
                        <th scope="row">
                            {{ $key }}
                        </th>
                        <td>
                            ${{ number_format($payment, 2, ".") }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Subtotal</td>
                        <th>${{ number_format($servicesPerPayments->sum(), 2, ".") }}</th>
                    </tr>
                    <tr>
                        <td>Gastos</td>
                        <th>${{ number_format($expenses->sum('amount'), 2, ".") }}</th>
                    </tr>
                    @foreach ($serviceswithinCash as $key => $payment)
                        <tr>
                            <td>{{ $key }}</td>
                            <th>${{ number_format($payment, 2, ".") }}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Total</td>
                        <th>${{ number_format($servicesPerPayments->sum() - $expenses->sum('amount') - $serviceswithinCash->sum(), 2, ".") }}</th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>