@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Inventario')
@section('tab_title', 'Inventario | ' . config('app.name'))
@section('description', 'Inventario.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Inventario de {{ $user->name }}
        </h1>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Inventarios actuales
            </h3>

            @if (! $inventory->count())
                <p class="text-center py-1">
                    Por el momento no hay elementos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $inventory }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Producto</th>
                                <th>Cantidad </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inventoryItem in resourceList" class="table-resource__row" :key="inventoryItem.id">
                                <td data-label="Producto:">
                                    @{{ inventoryItem.template.product_name }}
                                </td>
                                <td data-label="Cantidad:">
                                    @{{ inventoryItem.quantity }}
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>
            @endif

        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Entradas
            </h3>

            @if (! $inventory->count())
                <p class="text-center py-1">
                    Por el momento no hay elementos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsEntradas }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inventoryItem in resourceList" class="table-resource__row" :key="inventoryItem.id">
                                <td data-label="Fecha:">
                                    @{{ new Date(inventoryItem.date).toLocaleDateString('es-MX') }}
                                </td>
                                <td data-label="Producto:">
                                    @{{ inventoryItem.inventory.template.product_name }}
                                </td>
                                <td data-label="Cantidad:">
                                    @{{ inventoryItem.quantity }}
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>
            @endif

        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Salidas
            </h3>

            @if (! $inventory->count())
                <p class="text-center py-1">
                    Por el momento no hay elementos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsSalidas }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Nota</th>
                                <th>Producto</th>
                                <th>Cantidad </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inventoryItem in resourceList" class="table-resource__row" :key="inventoryItem.id">
                                <td data-label="Fecha:">
                                    @{{ new Date(inventoryItem.date).toLocaleDateString('es-MX') }}
                                </td>
                                <td data-label="Nota:">
                                    @{{ inventoryItem.sale_id }}
                                </td>
                                <td data-label="Producto:">
                                    @{{ inventoryItem.inventory.template.product_name }}
                                </td>
                                <td data-label="Cantidad:">
                                    @{{ inventoryItem.quantity }}
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>
            @endif

        </section>
    </div>
@endsection
