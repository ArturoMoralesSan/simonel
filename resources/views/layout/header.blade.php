<div class="social-bar-container">
    <a style="margin-right: 5px;" href="https://www.facebook.com/unipolidgo"><img class="social-icon" src="{{ url('img/svg/facebook.svg') }}" alt=""></a>
    <a style="margin-right: 5px;" href="https://twitter.com/unipolidgo"><img class="social-icon" src="{{ url('img/svg/twitter.svg') }}" alt=""></a>
    <a href="https://www.instagram.com/unipolidgo/"><img class="social-icon" src="{{ url('img/svg/instagram.svg') }}" alt=""></a>
</div>
<site-header
    :logo="'{{ url('img/logo-imac.png') }}'"
    :uri="'{{ url('/') }}'"
    :breakpoint="760"
>
    <template slot="button-session">
        <a style="margin-right: 5px;" href="https://www.facebook.com/unipolidgo"><img class="social-icon" src="{{ url('img/svg/facebook.svg') }}" alt=""></a>
        <a style="margin-right: 5px;" href="https://twitter.com/unipolidgo"><img class="social-icon" src="{{ url('img/svg/twitter.svg') }}" alt=""></a>
        <a href="https://www.instagram.com/unipolidgo/"><img class="social-icon" src="{{ url('img/svg/instagram.svg') }}" alt=""></a>
    </template>
</site-header>

{{-- <div class="menu__container">
    <site-menu
    :breakpoint="760"
    :links="{
        'Inicio': '{{ url('/') }}',
        'Padrón de artistas': '{{ url('padron-artistas') }}',
    }"
    active-link="{{ $activeLink }}"
    >
        <template slot="close">
            Cerrar @svg('x', 'main-menu__close-icon')
        </template>
    </site-menu>
    @guest
        <a href="{{ url('iniciar-sesion') }}" class="btn btn--outline-primary btn-login-menu">
            <img class="img-user-login" src="{{ url('img/header/user.svg') }}">
            Ingresar
         </a>
    @endguest
</div> --}}
