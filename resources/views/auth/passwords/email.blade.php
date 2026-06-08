@extends('layout.master')
@section('title', config('app.name'))
@section('description', 'Restablecer contraseña.')
@section('canonical', config('app.url'))
@section('class', 'home')
@section('content')
<section class="section section-login">
    <div class="container">         
        <div class="login-form flex-col">
            <img class="login-image" src="{{ url('img/advantage-logo.png') }}" alt="">
            <form class="form-boxed" method="POST" action="{{ route('password.email') }}">
                @csrf
                <h1 class="h3 text-center">Restablecer contraseña</h1>
                <hr></hr>
                <div class="form-control">
                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn--brand w-full">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
