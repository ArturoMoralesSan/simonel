@extends('layout.dashboard-master')

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

    {{-- INVENTARIO --}}
    <section class="db-panel">
        <h3 class="db-panel__title">
            Inventarios actuales
        </h3>

        @if (!$inventory->count())
            <p class="text-center py-1">
                Por el momento no hay elementos registrados.
            </p>
        @else

            <resource-table :breakpoint="800" :model="{{ $inventory }}" inline-template>
                <table class="table size-caption mx-auto md:table--responsive">
                    <thead>
                        <tr class="table-resource__headings">
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="inventoryItem in resourceList"
                            :key="inventoryItem.id"
                            class="table-resource__row">

                            <td>@{{ inventoryItem.product.manufactured.name }} <span class="description">@{{ inventoryItem.product.manufactured.description }}</span></td>
                            <td>@{{ inventoryItem.quantity }}</td>

                        </tr>
                    </tbody>
                </table>
            </resource-table>

        @endif
    </section>

    {{-- ========================= --}}
    {{-- MAYORISTA --}}
    {{-- ========================= --}}
    @if($clienteTipo === 'mayorista')

        {{-- ENTRADAS --}}
        <section class="db-panel">
            <h3 class="db-panel__title">
                En cámara de refrigeración
            </h3>

            @if (!$movementsEntradas->count())
                <p class="text-center py-1">
                    No hay registros.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsEntradas }}" inline-template>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in resourceList" :key="item.id">
                                <td>@{{ new Date(item.date).toLocaleDateString('es-MX') }}</td>
                                <td>@{{ item.inventory.product.manufactured.name }} <span class="description">@{{ item.inventory.product.manufactured.description }}</span></td>
                                <td>@{{ item.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </resource-table>

            @endif
        </section>

        {{-- SALIDAS --}}
        <section class="db-panel">
            <h3 class="db-panel__title">
                En piso de venta
            </h3>

            @if (!$movementsSalidas->count())
                <p class="text-center py-1">
                    No hay registros.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsSalidas }}" inline-template>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in resourceList" :key="item.id">
                                <td>@{{ new Date(item.date).toLocaleDateString('es-MX') }}</td>
                                <td>@{{ item.sale_id }}</td>
                                <td>@{{ item.inventory.product.manufactured.name }} <span class="description">@{{ item.inventory.product.manufactured.description }}</span></td>
                                <td>@{{ item.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </resource-table>

            @endif
        </section>
        {{-- MERMAS --}}
        <section class="db-panel">
            <h3 class="db-panel__title">
                Mermas
            </h3>

            @if (!$movementsMermas->count())
                <p class="text-center py-1">
                    No hay registros.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsMermas }}" inline-template>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in resourceList" :key="item.id">
                                <td>
                                    @{{ new Date(item.date).toLocaleDateString('es-MX') }}
                                </td>
                                <td>
                                    @{{ item.inventory.product.manufactured.name }}
                                    <span class="description">
                                        @{{ item.inventory.product.manufactured.description }}
                                    </span>
                                </td>
                                <td>
                                    @{{ item.quantity }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </resource-table>

            @endif
        </section>

    @else

        {{-- ========================= --}}
        {{-- MINORISTA --}}
        {{-- ========================= --}}
        <section class="db-panel">
            <h3 class="db-panel__title">
                Ventas
            </h3>

            @if (!$movementsEntradas->count())
                <p class="text-center py-1">
                    No hay ventas registradas.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $movementsEntradas }}" inline-template>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in resourceList" :key="item.id">
                                <td>@{{ new Date(item.date).toLocaleDateString('es-MX') }}</td>
                                <td>@{{ item.inventory.product.manufactured.name }} <span class="description">@{{ item.inventory.product.manufactured.description }}</span></td>
                                <td>@{{ item.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </resource-table>

            @endif
        </section>

    @endif

</div>

@endsection