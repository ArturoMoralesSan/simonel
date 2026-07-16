@extends('layout.dashboard-master')

@section('title', 'Detalle cliente')
@section('tab_title', 'Detalle cliente | ' . config('app.name'))
@section('description', 'Detalle del cliente.')
@section('css_classes', 'dashboard')


@section('content')

<section class="mb-16">

    <div class="dashboard-heading">

        <h1 class="dashboard-heading__title">
            {{ $customer->business_name }}
        </h1>

    </div>


    <div class="fluid-container">

        @include('components.alert')


        <p class="mb-8">
            <span class="color-link">«</span>
            <a href="{{ url('admin/clientes') }}">
                Volver a clientes
            </a>
        </p>


        {{-- Datos generales --}}
        <section class="db-panel">

            <h3 class="db-panel__title">
                Datos generales
            </h3>


            <div class="row">

                <div class="md:col-1/3">
                    <strong>Razón social</strong>
                    <p>
                        {{ $customer->business_name }}
                    </p>
                </div>


                <div class="md:col-1/3">
                    <strong>RFC</strong>
                    <p>
                        {{ $customer->rfc ?? '-' }}
                    </p>
                </div>


                <div class="md:col-1/3">
                    <strong>Tipo cliente</strong>
                    <p>
                        {{ ucfirst($customer->customer_type) }}
                    </p>
                </div>

            </div>


            <div class="row mt-4">

                <div class="md:col-1/3">
                    <strong>Teléfono</strong>
                    <p>
                        {{ $customer->phone ?? '-' }}
                    </p>
                </div>


                <div class="md:col-1/3">
                    <strong>Email</strong>
                    <p>
                        {{ $customer->email ?? '-' }}
                    </p>
                </div>


                <div class="md:col-1/3">
                    <strong>Vendedor</strong>
                    <p>
                        {{ $customer->seller->name ?? '-' }}
                    </p>
                </div>

            </div>

        </section>



        {{-- Crédito --}}
        <section class="db-panel mt-8">

            <h3 class="db-panel__title">
                Crédito
            </h3>


            @php

                $debt = $customer->accountsReceivable()
                    ->where('balance','>',0)
                    ->sum('balance');

            @endphp


            <div class="row">


                <div class="md:col-1/2">
                    <strong>
                        Tipo de cliente
                    </strong>
                    <p>
                        {{ ucfirst($customer->customer_type) }}
                    </p>
                </div>

                <div class="md:col-1/2">

                    <strong>
                        Saldo pendiente
                    </strong>

                    <p>
                        ${{ number_format($debt,2) }}
                    </p>

                </div>
            </div>
        </section>




        {{-- Autorizaciones --}}
        <section class="db-panel mt-8">


            <h3 class="db-panel__title">
                Autorizaciones de crédito
            </h3>


            @if(!$customer->creditAuthorizations->count())

                <p class="text-center">
                    No existen autorizaciones registradas.
                </p>


            @else


                <table class="table">

                    <thead>

                        <tr>
                            <th>
                                Fecha
                            </th>

                            <th>
                                Autorizó
                            </th>

                            <th>
                                Motivo
                            </th>

                            <th>
                                Vigencia
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    @foreach($customer->creditAuthorizations as $authorization)

                        <tr>

                            <td>
                                {{ $authorization->created_at->format('d/m/Y') }}
                            </td>


                            <td>
                                {{ $authorization->authorizer->name ?? '-' }}
                            </td>


                            <td>
                                {{ $authorization->reason }}
                            </td>


                            <td>

                                @if($authorization->expires_at)

                                    {{ $authorization->expires_at->format('d/m/Y') }}

                                @else

                                    Sin vencimiento

                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        @if($isSuperAdmin)
            <section class="db-panel mt-8">

                <h3 class="db-panel__title">
                    Autorizar crédito
                </h3>


                <base-form 
                    action="{{ url('admin/clientes/autorizar-credito') }}"
                    inline-template
                    v-cloak
                >

                    <form>
                        <text-field
                            type="hidden"
                            name="customer_id"
                            v-model="fields.customer_id"
                            maxlength="10"
                            :initial="{{ $customer->id }}"
                        >
                        </text-field>


                        <div class="md:row mb-4">


                            <div class="md:col-1/2">

                                <div class="form-control">

                                    <label for="reason">
                                        Motivo
                                    </label>

                                    <text-field
                                        name="reason"
                                        v-model="fields.reason"
                                        maxlength="255"
                                        initial=""
                                    >
                                    </text-field>

                                    <field-errors name="reason"></field-errors>

                                </div>

                            </div>



                            <div class="md:col-1/2">

                                <div class="form-control">

                                    <label for="expires_at">
                                        Vigencia
                                    </label>


                                    <text-field
                                        name="expires_at"
                                        v-model="fields.expires_at"
                                        type="date"
                                        initial=""
                                    >
                                    </text-field>


                                    <field-errors name="expires_at"></field-errors>

                                </div>

                            </div>


                        </div>



                        <div class="text-center">

                            <form-button class="btn--success btn--wide">
                                Autorizar crédito
                            </form-button>

                        </div>


                    </form>


                </base-form>


            </section>
        @endif
    </div>
</section>



@endsection