@extends('layout.dashboard-master')

@section('title', 'Caducidades')

@section('content')

<div class="dashboard-heading">
    <h1 class="dashboard-heading__title">
        Control de Caducidades
    </h1>
</div>

<div class="fluid-container">

    {{-- =========================
        RESUMEN DASHBOARD
    ========================== --}}
    <section class="db-panel">

        <div class="row">

            <div class="md:col-1/3">
                <strong>Vencidos</strong>
                <p style="color: red; font-size: 22px;">
                    {{ $expiredCount }}
                </p>
            </div>

            <div class="md:col-1/3">
                <strong>Próximos 7 días</strong>
                <p style="color: #d39e00; font-size: 22px;">
                    {{ $next7DaysCount }}
                </p>
            </div>

            <div class="md:col-1/3">
                <strong>Próximos 30 días</strong>
                <p style="color: #17a2b8; font-size: 22px;">
                    {{ $next30DaysCount }}
                </p>
            </div>

        </div>

    </section>


    {{-- =========================
        FILTROS
    ========================== --}}
    <section class="db-panel" style="margin-top: 20px;">

        <form method="GET">

            <div class="row">

                <div class="md:col-1/2">
                    <strong>Tipo</strong>
                    <select name="type" class="form-field" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="mp" {{ $type == 'mp' ? 'selected' : '' }}>
                            Materia Prima
                        </option>
                        <option value="pt" {{ $type == 'pt' ? 'selected' : '' }}>
                            Producto Terminado
                        </option>
                    </select>
                </div>

                <div class="md:col-1/2">
                    <strong>Estado</strong>
                    <select name="status" class="form-field" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>
                            Vencidos
                        </option>
                        <option value="7days" {{ $status == '7days' ? 'selected' : '' }}>
                            7 días
                        </option>
                        <option value="30days" {{ $status == '30days' ? 'selected' : '' }}>
                            30 días
                        </option>
                    </select>
                </div>

            </div>

        </form>

    </section>


    {{-- =========================
        LISTA DE LOTES
    ========================== --}}
    <section class="db-panel" style="margin-top: 20px;">

        <div class="table-responsive">

            <table class="table">

                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Lote</th>
                        <th>Entrada</th>
                        <th>Caducidad</th>
                        <th>Cantidad</th>
                        <th>Días</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($lots as $lot)

                        <tr>

                            <td>{{ $lot['type'] }}</td>

                            <td>{{ $lot['name'] }} <span class="description">{{ $lot['description'] }}</span></td>

                            <td>{{ $lot['lot_number'] }}</td>

                            <td>{{ \Carbon\Carbon::parse($lot['entry_date'])->format('d/m/Y') }}</td>

                            <td>{{ \Carbon\Carbon::parse($lot['expiration_date'])->format('d/m/Y') }}</td>

                            <td>{{ $lot['quantity'] }}</td>

                            <td>
                                @if($lot['days_remaining'] < 0)
                                    <span style="color:red;font-weight:bold;">
                                        {{ $lot['days_remaining'] }} (Vencido)
                                    </span>
                                @elseif($lot['days_remaining'] <= 7)
                                    <span style="color:#d39e00;font-weight:bold;">
                                        {{ $lot['days_remaining'] }}
                                    </span>
                                @elseif($lot['days_remaining'] <= 30)
                                    <span style="color:#17a2b8;">
                                        {{ $lot['days_remaining'] }}
                                    </span>
                                @else
                                    <span style="color:green;">
                                        {{ $lot['days_remaining'] }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($lot['status'] == 'Vencido')
                                    <span class="badge badge-danger">Vencido</span>
                                @elseif($lot['status'] == 'Próximo a vencer')
                                    <span class="badge badge-warning">Próximo</span>
                                @elseif($lot['status'] == 'Atención')
                                    <span class="badge badge-info">Atención</span>
                                @else
                                    <span class="badge badge-success">Vigente</span>
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" style="text-align:center;">
                                No hay lotes con caducidad registrada
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection