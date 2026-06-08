<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="page_break mt-4">
        <img style="width: 160px;display: block;margin: 0 auto;" src="{{ url('img/imax-logo.png')}}" alt="">
        
        <div class="row">
            <div class="col-md-10">
                <h6>Reporte del periodo ({{ $start_date }} al {{ $end_date }})</h6>
                @foreach ($groupedExpenses as $key => $expenses)
                    <h4>{{$key}}</h4>
                    <table class="table table-bordered">
                        <caption>Lista de gastos</caption>
                        <thead>
                        <tr>
                            <th scope="col">Gasto</th>
                            <th scope="col">Monto</th>
                            <th scope="col">Fecha</th>
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
                                <td>
                                {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <th>${{ number_format($expenses->sum('amount'), 2, ".") }}</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>