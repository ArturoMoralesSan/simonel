@extends('layout.dashboard-master')

{{-- Metadata --}}
@section('title', 'Editar rol')
@section('tab_title', 'Editar rol | ' . config('app.name'))
@section('description', 'Editar rol.')
@section('css_classes', 'dashboard')

@section('content')

<section class="mb-16">
    <div class="dashboard-heading">
        <h1 class="dashboard-heading__title">
            Editar rol
        </h1>
    </div>

    <div class="fluid-container mb-16">
        <p class="mb-12">
            @include('components.alert')
            <span class="color-link">«</span>
            <a href="{{ url('admin/roles/') }}">Ver todos los roles</a>
        </p>

            <role-form action="{{ url('admin/roles/'. $role->id .'/actualizar') }}"
                method="put"
                enctype="multipart/form-data"
                inline-template
                v-cloak
            >
                <form>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Datos del rol
                        </h3>

                        <div class="md:row">
                            <div class="md:col-2/3">
                                <div class="form-control">
                                    <label for="name">Nombre</label>
                                    <text-field name="name" v-model="fields.name" maxlength="100" initial="{{ $role->name }}"></text-field>
                                    <field-errors name="name"></field-errors>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Permisos
                        </h3>

                        <div class="md:row">
                            <div class="md:col">
                                <div class="permissions-container">
                                    @foreach ($permissions as $permission)
                                        <label class="permission-item">
                                            <checkbox-field 
                                                :initial="{{ $role->key_name === 'superadmin' ? 'true' : ($role->hasPermission($permission->name) ? 'true' : 'false') }}"                                                v-model="fields.permissions_{{ $permission->id }}" 
                                                name="permissions_{{ $permission->id }}" 
                                            >
                                            </checkbox-field>
                                            {{ $permission->name }}
                                        </label>
                                    @endforeach
    

                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="text-center">
                        <form-button class="btn--blue--dashboard btn--wide">
                            Actualizar
                        </form-button>
                    </div>
                </form>
            </role-form>
    </div>
</section>

@endsection
