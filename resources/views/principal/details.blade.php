@extends('layout.master')

{{-- Metadata --}}
@section('title', config('app.name'))
@section('description', '')
@section('canonical', config('app.url'))
@section('class', 'home')
@section('content')
    <section class="section">
        <div class="container">
            <h1 class="h2 text-center mb-8"> {{ $artist->user->full_name }}</h1>
            <div class="row">
                <div class="col md:col-1/3">
                    <div class="mb-8">
                        <img src="{{ url('archivos/artistas/'.$artist->user->photo) }}" class="img-detail" alt="">

                        <img src="{{ url('archivos/artistas/'.$artist->qr_image) }}" class="img-detail__qr" alt="">

                    </div>
                    @if($artist->facebook || $artist->twitter || $artist->instagram)
                        <div class="form-group">
                            <h2 class="h5 form-group__title">Redes sociales</h2>
                            <div>
                                @if($artist->facebook)
                                    <a style="margin-right: 5px;" href="{{ $artist->facebook }}">
                                        <img class="social-icon-detail" src="{{ url('img/svg/facebook.svg') }}" alt="">
                                    </a>
                                @endif
                                @if($artist->twitter)
                                    <a style="margin-right: 5px;" href="{{ $artist->twitter }}">
                                        <img class="social-icon-detail" src="{{ url('img/svg/twitter.svg') }}" alt="">
                                    </a>
                                @endif
                                @if($artist->instagram)
                                    <a href="{{ $artist->instagram }}">
                                        <img class="social-icon-detail" src="{{ url('img/svg/instagram.svg') }}" alt="">
                                    </a>
                                @endif
                                @if($artist->youtube)
                                    <a href="{{ $artist->youtube }}">
                                        <img class="social-icon-detail" src="{{ url('img/svg/youtube.svg') }}" alt="">
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col md:col-2/3">
                    <dl class="form-group">
                        <h2 class="h5 form-group__title">Datos personales</h2>
                        <dt>Correo electrónico</dt>
                        <dd>{{ $artist->user->email }}</dd>
                        <dt>Teléfono celular</dt>
                        @if($artist->cellphone)
                            <dd>{{ $artist->cellphone }}</dd>
                        @else
                            <dd>-</dd>
                        @endif
                        <dt>Teléfono</dt>
                        @if($artist->telephone)
                            <dd>{{ $artist->telephone }}</dd>
                        @else
                            <dd>-</dd>
                        @endif
                    </dl>
                    <dl class="form-group">
                        <h2 class="h5 form-group__title">Datos profesionales</h2>
                        <dt>Actividad</dt>
                        <dd>{{ $artist->activity->name }}</dd>
                        <dt>Categoría</dt>
                        <dd>{{ $artist->category->name }}</dd>
                        <dt>Especialidad</dt>
                        <dd>{{ $artist->speciality }}</dd>
                    </dl>
                    <div class="form-group">
                        <h2 class="h5 form-group__title">Semblanza</h2>
                        @if($artist->sintax)
                        <p>
                            {{ $artist->sintax }}
                        </p>
                        @else
                        <p>
                            -
                        </p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection