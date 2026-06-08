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
                <table class="table table-bordered">
                    <caption>Lista de registrados</caption>
                    <thead>
                      <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Nombre completo</th>
                        <th scope="col">Edad</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Talla</th>
                        <th scope="col">Celular</th>
                        <th scope="col">Tipo de carrera</th>
                        <th scope="col">Monto</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($registers as $register)
                        <tr>
                            <th scope="row">{{ $register->id }}</th>
                            <td>{{ $register->formated_date }}</td>
                            <td>{{ $register->name }}</td>
                            <td>{{ $register->age }}</td>
                            <td>{{ $register->sex }}</td>
                            <td>{{ $register->size }}</td>
                            <td>{{ $register->cellphone }}</td>
                            <td>{{ $register->type }}</td>

                            <td>${{ number_format($register->cost, 2, ".") }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            
                            <td colspan="8">Total</td>
                            <th>${{ number_format($registers->sum('cost'), 2, ".") }}</th>
                            
                        </tr>
                    </tfoot>
                  </table>
            </div>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>