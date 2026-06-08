@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'materia prima')
@section('tab_title', 'materia prima | ' . config('app.name'))
@section('description', 'Lista de materia prima.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Materia prima
        </h1>

        <p class="dashboard-heading__caption">
            Hay {{ $material->count() }} materia prima registrada.
        </p>
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de materia prima
            </h3>

            @if (!$material->count())
                <p class="text-center py-1">
                    Por el momento no hay materia prima registrada.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $material }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>SKU</th>
                                <th>materia prima</th>
                                <th>Tipo de materia</th>
                                <th>Unidad</th>
                                <th>Días de expiración</th>
                                <th>Costo</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="cutItem in resourceList" class="table-resource__row" :key="cutItem.id">
                                <td data-label="SKU:">
                                    @{{ cutItem.sku }}
                                </td>
                                <td data-label="Nombre:">
                                    @{{ cutItem.name }}
                                </td>
                                <td data-label="Tipo de materia:">
                                    @{{ cutItem.raw_material_type }}
                                </td>

                                <td data-label="Unidad:">
                                    @{{ cutItem.unit }}
                                </td>
                                <td data-label="Días de expiración:">
                                    @{{ cutItem.expiration_days }}
                                </td>
                                
                                <td data-label="Costo:">
                                    $@{{ cutItem.cost }}
                                </td>

                                <td class="table-resource__actions" data-label="Acciones:">
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/materia-prima/' + cutItem.id + '/editar' ">
                                        <img class="svg-icon" src="{{ url('img/svg/edit.svg')}}">
                                        Editar
                                    </a>
                                    <delete-button class="btn--danger table-resource__button" :url="$root.path + '/admin/materia-prima/eliminar/' + cutItem.id"
                                        :resource-id="cutItem.id"
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
