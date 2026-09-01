@extends('layout.dashboard-master')

@section('meta.title', 'Reportes')

@section(
    'meta.tab_title',
    'Reportes | ' . config('app.name')
)

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


    {{-- =========================================================
        RESUMEN GENERAL
    ========================================================== --}}

    <section class="db-panel mb-8">

        <div class="row">

            <div class="column-statistics">

                <strong>
                    $
                    {{ number_format(
                        $summary['totalSales'] ?? 0,
                        2
                    ) }}
                </strong>

                <br>

                Total vendido

            </div>


            <div class="column-statistics">

                <strong>
                    {{ number_format(
                        $summary['salesCount'] ?? 0
                    ) }}
                </strong>

                <br>

                Ventas

            </div>


            <div class="column-statistics">

                <strong>
                    {{ number_format(
                        $summary['customersCount'] ?? 0
                    ) }}
                </strong>

                <br>

                Clientes

            </div>


            <div class="column-statistics">

                <strong>
                    {{ number_format(
                        $summary['productsSold'] ?? 0,
                        2
                    ) }}
                </strong>

                <br>

                Productos vendidos

            </div>

        </div>

    </section>



    {{-- =========================================================
        VENDEDORES / PRODUCTOS
    ========================================================== --}}

    <div class="md:row">


        {{-- VENDEDORES --}}

        <div class="md:col-1/2">

            <section class="db-panel">

                <h3 class="db-panel__title d-flex items-center justify-between">

                    Resumen por vendedores

                    <a
                        href="{{ url(
                            'admin/reportes/vendedores/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
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

                                    <th>
                                        Vendedor
                                    </th>

                                    <th>
                                        Ventas
                                    </th>

                                    <th>
                                        Total vendido
                                    </th>

                                    @foreach($paymentMethods as $method)

                                        <th>
                                            {{ ucfirst($method) }}
                                        </th>

                                    @endforeach

                                    <th>
                                        PDF
                                    </th>

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

                                        @{{ Number(
                                            seller.sales_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Total vendido">

                                        $
                                        @{{ Number(
                                            seller.total_sales || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

                                    </td>


                                    @foreach($paymentMethods as $method)

                                        @php

                                            $paymentKey =
                                                'payment_' .
                                                \Illuminate\Support\Str::slug(
                                                    $method,
                                                    '_'
                                                );

                                        @endphp

                                        <td
                                            data-label="{{ ucfirst($method) }}"
                                        >

                                            <span
                                                v-text="
                                                    Number(
                                                        seller.{{ $paymentKey }} || 0
                                                    ).toLocaleString('en-US', {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    })
                                                "
                                            ></span>

                                        </td>

                                    @endforeach


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

            </section>

        </div>



        {{-- PRODUCTOS --}}

        <div class="md:col-1/2">

            <section class="db-panel">

                <h3 class="db-panel__title d-flex items-center justify-between">

                    Resumen de productos

                    <a
                        href="{{ url(
                            'admin/reportes/productos/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
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

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Ventas
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Total
                                    </th>

                                    <th>
                                        PDF
                                    </th>

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

                                        @{{ Number(
                                            product.sales_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Cantidad">

                                        @{{ Number(
                                            product.quantity || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 3,
                                            maximumFractionDigits: 3
                                        }) }}

                                    </td>


                                    <td data-label="Total">

                                        $
                                        @{{ Number(
                                            product.total || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

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



    {{-- =========================================================
        CLIENTES / MÉTODOS DE PAGO
    ========================================================== --}}

    <div class="md:row mt-8">


        {{-- CLIENTES --}}

        <div class="md:col-1/2">

            <section class="db-panel">

                <h3 class="db-panel__title d-flex items-center justify-between">

                    Resumen de clientes

                    <a
                        href="{{ url(
                            'admin/reportes/clientes/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
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

                                    <th>
                                        Cliente
                                    </th>

                                    <th>
                                        Compras
                                    </th>

                                    <th>
                                        Total
                                    </th>

                                    <th>
                                        PDF
                                    </th>

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

                                        @{{ Number(
                                            customer.sales_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Total">

                                        $
                                        @{{ Number(
                                            customer.total || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

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
                        href="{{ url(
                            'admin/reportes/pagos/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
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

                                    <th>
                                        Método de pago
                                    </th>

                                    <th>
                                        Cantidad de operaciones
                                    </th>

                                    <th>
                                        Total
                                    </th>

                                    <th>
                                        PDF
                                    </th>

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

                                        @{{ Number(
                                            payment.operations || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Total">

                                        $
                                        @{{ Number(
                                            payment.total || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

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



    {{-- =========================================================
        ALMACENES
    ========================================================== --}}

    <div class="md:row mt-8">


        {{-- =====================================================
            ALMACENES DE MATERIAS PRIMAS
        ====================================================== --}}

        <div class="md:col-1/2">

            <section class="db-panel">

                <h3 class="db-panel__title d-flex items-center justify-between">

                    Resumen por almacén de materias primas

                    <a
                        href="{{ url(
                            'admin/reportes/primas/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                        class="btn btn-nowrap btn--xs table-resource__button"
                    >
                        Imprimir todo
                    </a>

                </h3>


                <div class="md:row mb-4">

                    <resource-table
                        :breakpoint="800"
                        :model="{{ $rawMaterialWarehouses }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>

                                <tr>

                                    <th>
                                        Almacén
                                    </th>

                                    <th>
                                        Lotes
                                    </th>

                                    <th>
                                        Materias primas
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Total
                                    </th>

                                    <th>
                                        PDF
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr
                                    v-for="warehouse in resourceList"
                                    :key="warehouse.id"
                                >

                                    <td data-label="Almacén">

                                        @{{ warehouse.name }}

                                    </td>


                                    <td data-label="Lotes">

                                        @{{ Number(
                                            warehouse.lots_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Materias primas">

                                        @{{ Number(
                                            warehouse.materials_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Cantidad">

                                        @{{ Number(
                                            warehouse.quantity || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

                                    </td>


                                    <td data-label="Total">

                                        $
                                        @{{ Number(
                                            warehouse.total || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

                                    </td>
                                    <td>
                                        <link-pdf
                                            :branchid="warehouse.id"
                                            url="/admin/reportes/primas/"
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



        {{-- =====================================================
            ALMACENES DE PRODUCTOS TERMINADOS
        ====================================================== --}}

        <div class="md:col-1/2">

            <section class="db-panel">

                <h3 class="db-panel__title d-flex items-center justify-between">

                    Resumen por Almacén de producto terminado

                    <a
                        href="{{ url(
                            'admin/reportes/terminado/pdf'
                        ) }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
                        class="btn btn-nowrap btn--xs table-resource__button"
                    >
                        Imprimir todo
                    </a>

                </h3>


                <div class="md:row mb-4">

                    <resource-table
                        :breakpoint="800"
                        :model="{{ $productWarehouses }}"
                        inline-template
                    >

                        <table class="table size-caption mx-auto md:table--responsive">

                            <thead>

                                <tr>

                                    <th>
                                        Almacén
                                    </th>

                                    <th>
                                        Lotes
                                    </th>

                                    <th>
                                        Productos
                                    </th>

                                    <th>
                                        Cantidad
                                    </th>

                                    <th>
                                        Total
                                    </th>
                                    <th>
                                        PDF
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr
                                    v-for="warehouse in resourceList"
                                    :key="warehouse.id"
                                >

                                    <td data-label="Almacén">

                                        @{{ warehouse.name }}

                                    </td>


                                    <td data-label="Lotes">

                                        @{{ Number(
                                            warehouse.lots_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Productos">

                                        @{{ Number(
                                            warehouse.products_count || 0
                                        ).toLocaleString('en-US') }}

                                    </td>


                                    <td data-label="Cantidad">

                                        @{{ Number(
                                            warehouse.quantity || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

                                    </td>


                                    <td data-label="Total">

                                        $
                                        @{{ Number(
                                            warehouse.total || 0
                                        ).toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) }}

                                    </td>
                                    <td>
                                        <link-pdf
                                            :branchid="warehouse.id"
                                            url="/admin/reportes/terminado/"
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