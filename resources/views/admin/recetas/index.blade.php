@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Recetas')
@section('tab_title', 'Recetas | ' . config('app.name'))
@section('description', 'Lista de Recetas.')
@section('css_classes', 'dashboard')

@section('content')
    <div class="dashboard-heading">
        <div class="md:row justify-between">
            <div class="md:col-1/2">
                <h1 class="dashboard-heading__title">
                    Recetas
                </h1>
                <p class="dashboard-heading__caption">
                    Hay {{ $recipes->count() }} recetas registradas.
                </p>
            </div>
            <div class="md:col-1/2 d-flex items-center">
                
            </div>
        </div>
        
    </div>

    <div class="fluid-container mb-16">
        @include('components.alert')
           
        <section class="db-panel">
            <h3 class="db-panel__title">
                Lista de recetas
            </h3>

            @if (!$recipes->count())
                <p class="text-center py-1">
                    Por el momento no hay recetas registradas.
                </p>
            @else

                <resource-table :breakpoint="800" :model="{{ $recipesItems }}" inline-template>
                    <table class="table size-caption mx-auto mb-16 md:table--responsive">

                        <thead>
                            <tr class="table-resource__headings">
                                <th>Producto</th>
                                <th>Rendimiento</th>
                                <th>Ingredientes</th>
                                <th>Notas</th>
                                <th class="pr-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="recipe in resourceList"
                                class="table-resource__row"
                                :key="recipe.id"
                            >

                                <td data-label="Producto:">
                                    @{{ recipe.manufactured.name }} <span class="description">@{{ recipe.manufactured.description }}</span>
                                </td>

                                <td data-label="Rendimiento:">
                                    @{{ recipe.yield_quantity }}
                                    @{{ recipe.unit }}
                                </td>

                                <td data-label="Ingredientes:">
                                    @{{ recipe.items.length }}
                                </td>

                                <td data-label="Notas:">
                                    @{{ recipe.notes }}
                                </td>

                                <td
                                    class="table-resource__actions"
                                    data-label="Acciones:"
                                >
                                    <a class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2" :href="$root.path + '/admin/recetas/' + recipe.id + '/detalle'">
                                        <img class="svg-icon-only" src="{{ url('img/svg/order.svg')}}">
                                    </a>

                                    <a
                                        class="btn btn-nowrap btn--sm btn--blue table-resource__button mr-2"
                                        :href="$root.path + '/admin/recetas/' + recipe.id + '/editar'"
                                    >
                                        <img
                                            class="svg-icon"
                                            src="{{ url('img/svg/edit.svg')}}"
                                        >
                                        Editar
                                    </a>

                                    <delete-button
                                        class="btn--danger table-resource__button"
                                        :url="$root.path + '/admin/recetas/eliminar/' + recipe.id"
                                        :resource-id="recipe.id"
                                        :options="{ onDelete: onResourceDelete }"
                                    >
                                        <img
                                            class="svg-icon"
                                            src="{{ url('img/svg/trash.svg')}}"
                                        >
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
