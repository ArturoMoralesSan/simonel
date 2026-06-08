@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Tipos de gastos')
@section('tab_title', 'Tipos de gastos | ' . config('app.name'))
@section('description', 'Lista de Tipos de gastos.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Tipos de gastos
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $type_expenses->count() }} tipos de gastos registrados.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de tipos de gastos
            </h3>

            @if (! $type_expenses->count())
                <p class="text-center py-1">
                    Por el momento no hay tipo de gastos registrados.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $type_expenses }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Nombre</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="typeExpenseItem in resourceList" class="table-resource__row" :key="typeExpenseItem.id">
                                <td data-label="Nombre:">
                                    @{{ typeExpenseItem.name }}
                                </td>
                                

                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/tipos-gastos/' + typeExpenseItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/tipos-gastos/eliminar/' + typeExpenseItem.id"
                                        :resource-id="typeExpenseItem.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img class="svg-icon" src="{{ url('img/svg/trash.svg')}}">
                                        Eliminar
                                    </delete-button>
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </resource-table>

            @endif

        </section>
    </div>
@endsection
