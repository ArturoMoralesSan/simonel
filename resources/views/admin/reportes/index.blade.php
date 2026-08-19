@extends('layout.dashboard-master')

@section('meta.title', 'Reportes')
@section('meta.tab_title', 'Reportes | ' . config('app.name'))
@section('css_classes', 'dashboard')

@section('content')

<div class="dashboard-heading">
    <div class="md:row justify-between items-center">

        <div class="md:col-1/2">
            <h1 class="dashboard-heading__title">
                Reportes
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

<div class="fluid-container">

    @include('components.alert')

    {{-- =========================
        RESUMEN GENERAL
    ========================== --}}

    <section class="db-panel mb-8">

        <div class="row">

            <div class="column-statistics">
                <strong>$ {{ number_format($summary['totalSales'] ?? 0,2) }}</strong>
                <br>
                Total vendido
            </div>

            <div class="column-statistics">
                <strong>{{ $summary['salesCount'] ?? 0 }}</strong>
                <br>
                Ventas
            </div>

            <div class="column-statistics">
                <strong>{{ $summary['customersCount'] ?? 0 }}</strong>
                <br>
                Clientes
            </div>

            <div class="column-statistics">
                <strong>{{ $summary['productsSold'] ?? 0 }}</strong>
                <br>
                Productos vendidos
            </div>

        </div>

    </section>


    <div class="md:row">

        {{-- VENDEDORES --}}

        <div class="md:col-1/2">

            <section class="db-panel">
                <h3 class="db-panel__title d-flex items-center justify-between">
                    Resumen por vendedores
                        <a
                            href="{{ url('admin/reportes/vendedores/pdf') }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                            class="btn btn-nowrap btn--xs table-resource__button"
                        >
                            Imprimir todo
                        </a>
                </h3>
                

                <div class="md:row mb-4">
                    <resource-table
                        :breakpoint="800"
                        :model="{{ $sellers }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>
                                <tr>
                                    <th>Vendedor</th>
                                    <th>Ventas</th>
                                    <th>Total vendido</th>
                                    <th>Efectivo</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="seller in resourceList"
                                    :key="seller.id"
                                >

                                    <td data-label="Vendedor">
                                        @{{ seller.name }}
                                    </td>

                                    <td data-label="Ventas">
                                        @{{ seller.sales_count }}
                                    </td>

                                    <td data-label="Total vendido">
                                        $ @{{ Number(seller.total_sales).toFixed(2) }}
                                    </td>

                                    <td data-label="Efectivo">
                                        $ @{{ Number(seller.cash_total).toFixed(2) }}
                                    </td>

                                    <td data-label="PDF">

                                        <link-pdf
                                            :branchid="seller.id"
                                            url="/admin/reportes/vendedor/"
                                            startdate="{{ request('start_date') }}"
                                            enddate="{{ request('end_date') }}"
                                        >
                                        </link-pdf>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </resource-table>
                        
                </div>

                {{-- Tabla vendedores --}}

            </section>

        </div>


        {{-- PRODUCTOS --}}

        <div class="md:col-1/2">

            <section class="db-panel">
                <h3 class="db-panel__title d-flex items-center justify-between">
                    Resumen de productos
                    <a
                        href="{{ url('admin/reportes/productos/pdf') }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                        class="btn btn-nowrap btn--xs table-resource__button"
                    >
                        Imprimir todo
                    </a>
                </h3>
                

                <div class="md:row mb-4">
                    <resource-table
                        :breakpoint="800"
                        :model="{{ $products }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ventas</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="product in resourceList"
                                    :key="product.id"
                                >

                                    <td data-label="Producto">
                                        @{{ product.name }}
                                    </td>

                                    <td data-label="Ventas">
                                        @{{ product.sales_count }}
                                    </td>

                                    <td data-label="Cantidad">
                                        @{{ Number(product.quantity).toFixed(3) }}
                                    </td>

                                    <td data-label="Total">
                                        $ @{{ Number(product.total).toFixed(2) }}
                                    </td>

                                    <td data-label="PDF">
                                        <link-pdf
                                            :branchid="product.id"
                                            url="/admin/reportes/producto/"
                                            startdate="{{ request('start_date') }}"
                                            enddate="{{ request('end_date') }}"
                                        >
                                        </link-pdf>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </resource-table>
                        
                </div>

            </section>

        </div>

    </div>


    <div class="md:row mt-8">

        {{-- CLIENTES --}}

        <div class="md:col-1/2">

            <section class="db-panel">
                <h3 class="db-panel__title d-flex items-center justify-between">
                    Resumen de clientes
                        <a
                        href="{{ url('admin/reportes/clientes/pdf') }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                        class="btn btn-nowrap btn--xs table-resource__button"
                    >
                        Imprimir todo
                    </a>
                </h3>
                <div class="md:row mb-4">
                    <resource-table
                        :breakpoint="800"
                        :model="{{ $customers }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Compras</th>
                                    <th>Total</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="customer in resourceList"
                                    :key="customer.id"
                                >

                                    <td data-label="Clientes">
                                        @{{ customer.name }}
                                    </td>

                                    <td data-label="Compras">
                                        @{{ customer.sales_count }}
                                    </td>

                                    <td data-label="Total">
                                        $ @{{ Number(customer.total).toFixed(2) }}
                                    </td>

                                    <td data-label="PDF">
                                        <link-pdf
                                            :branchid="customer.id"
                                            url="/admin/reportes/cliente/"
                                            startdate="{{ request('start_date') }}"
                                            enddate="{{ request('end_date') }}"
                                        >
                                        </link-pdf>
                                        
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </resource-table>
                </div>
            </section>

        </div>


        {{-- MÉTODOS DE PAGO --}}

        <div class="md:col-1/2">

            <section class="db-panel">
                <h3 class="db-panel__title d-flex items-center justify-between">
                    Resumen por métodos de pago
                        <a
                        href="{{ url('admin/reportes/pagos/pdf') }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                        class="btn btn-nowrap btn--xs table-resource__button"
                    >
                        Imprimir todo
                    </a>
                </h3>
                
                <div class="md:row mb-4">
                    <resource-table
                        :breakpoint="800"
                        :model="{{ $payments }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>
                                <tr>
                                    <th>Método de pago</th>
                                    <th>Cantidad de operaciones</th>
                                    <th>Total</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="payment in resourceList"
                                    :key="payment.id"
                                >

                                    <td data-label="Método de pago">
                                        @{{ payment.name }}
                                    </td>

                                    <td data-label="Cantidad de operaciones">
                                        @{{ payment.operations }}
                                    </td>

                                    <td data-label="Total">
                                        $ @{{ Number(payment.total).toFixed(2) }}
                                    </td>

                                    <td data-label="PDF">
                                        <link-pdf
                                            :branchid="payment.id"
                                            url="/admin/reportes/pago/"
                                            startdate="{{ request('start_date') }}"
                                            enddate="{{ request('end_date') }}"
                                        >
                                        </link-pdf>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </resource-table>
                </div>
                

            </section>

        </div>

    </div>

</div>

@endsection