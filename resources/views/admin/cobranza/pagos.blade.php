@extends('layout.dashboard-master')

@section('title', 'Registrar pago')
@section('tab_title', 'Registrar pago | ' . config('app.name'))
@section('description', 'Registrar pago de cuenta por cobrar.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">

    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Registrar pago
        </h1>
    </div>


    <div class="fluid-container mb-16">

        @include('components.alert')

        <p class="mb-12">

            <span class="color-link">«</span>

            <a href="{{ url('admin/cuentas-pendientes/'.$account->id) }}">
                Volver a cuenta
            </a>

        </p>


        <base-form
            action="{{ url('admin/cuentas-pendientes/'.$account->id.'/pago') }}"
            inline-template
            v-cloak
        >

            <form>
                {{-- Información cuenta --}}
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Cuenta por cobrar
                    </h3>
                    <div class="row">
                        <div class="md:col-1/3">
                            <strong>
                                Cliente
                            </strong>
                            <p>
                                {{ $account->customer->business_name }}
                            </p>
                        </div>
                        <div class="md:col-1/3">
                            <strong>
                                Total
                            </strong>
                            <p>
                                ${{ number_format($account->original_amount,2) }}
                            </p>
                        </div>
                        <div class="md:col-1/3">
                            <strong>
                                Saldo pendiente
                            </strong>

                            <p>
                                ${{ number_format($account->balance,2) }}
                            </p>
                        </div>
                    </div>
                </section>
                {{-- Pago --}}
                <section class="db-panel mt-8">
                    <h3 class="db-panel__title">
                        Datos del pago
                    </h3>
                    <div class="md:row mb-2">
                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>
                                    Método de pago
                                </label>
                                <select-field
                                    name="payment_id"
                                    v-model="fields.payment_id"
                                    :options="{{ $payments }}"
                                >
                                </select-field>
                                <field-errors name="payment_id"></field-errors>
                            </div>
                        </div>
                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label>
                                    Monto
                                </label>
                                <text-field

                                    name="amount"

                                    type="number"

                                    step="0.01"

                                    v-model="fields.amount"

                                    initial="{{ $account->balance }}"

                                >
                                </text-field>
                                <field-errors name="amount"></field-errors>
                            </div>
                        </div>

                    </div>

                    <div class="md:row">

                        <div class="md:col">

                            <div class="form-control">

                                <label>
                                    Notas
                                </label>


                                <text-area

                                    name="notes"

                                    v-model="fields.notes"

                                    class="form-control"

                                ></text-area>


                                <field-errors name="notes"></field-errors>


                            </div>

                        </div>


                    </div>



                </section>



                <div class="text-center">

                    <form-button class="btn--success btn--wide">

                        Registrar pago

                    </form-button>

                </div>


            </form>


        </base-form>


    </div>

</section>


@endsection
