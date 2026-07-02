<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .row {
            display: block;
            width: 100%;
        }

        .col-6 {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background: #1f2937;
            color: white;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background: #f3f4f6;
        }

        .badge {
            padding: 4px 8px;
            font-size: 10px;
            background: #e5e7eb;
            border-radius: 4px;
            display: inline-block;
        }

        .total-box {
            text-align: right;
            margin-top: 20px;
            font-size: 14px;
        }

        .total-box span {
            font-weight: bold;
            font-size: 16px;
        }

        .muted {
            color: #6b7280;
            font-size: 11px;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="title">REPORTE DE CLIENTES</div>
</div>

<!-- INFO USER + CUSTOMER -->
<div class="card">
    <div class="row">

        <div class="col-6">
            <strong>CLIENTE:</strong><br>
            {{ $customer->business_name ?? 'N/A' }}<br>
            <span class="muted">{{ $user->email ?? '' }}</span>
        </div>

        <div class="col-6">
            <strong>RFC:</strong><br>
            {{ $customer->rfc ?? '' }}
        </div>

    </div>

    <br>

    <span class="badge">
        Periodo: {{ $start_date }} → {{ $end_date }}
    </span>
</div>

<!-- TABLA VENTAS -->
<table>
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="60%">Productos</th>
            <th width="15%">Descuento</th>
            <th width="15%">Total</th>
        </tr>
    </thead>

    <tbody>
        @forelse($sales as $sale)
            <tr>
                <td>#{{ $sale->id }}</td>

                <td>
                    {{ $sale->products_list ?? 'Sin productos' }}
                </td>

                <td>
                    $ {{ number_format($sale->discount ?? 0, 2) }}
                </td>

                <td>
                    $ {{ number_format($sale->total_with_iva ?? 0, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;">
                    Sin ventas en este periodo
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- TOTAL -->
<div class="total-box">
    TOTAL GENERAL: <span>$ {{ number_format($totalGeneral ?? 0, 2) }}</span>
</div>

</body>
</html>