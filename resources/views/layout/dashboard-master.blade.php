<!doctype html>
    <html class="no-js" lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="cleartype" content="on">
        <meta name="apple-mobile-web-app-capable" content="yes">

        <!-- DNS prefetch -->
        <link rel="dns-prefetch" href="//fonts.googleapis.com">

        <title>@yield('meta.tab_title', config('app.name'))</title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="robots" content="noindex, nofollow">

        <!-- Icons -->
        @include('layout.icons')

        <!-- CSS -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700,700i" rel="stylesheet">
        <link rel="stylesheet" href="{{ version('css/dashboard.css') }}">
        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#436eb3">
        

    </head>

    <body data-root="{{ url('/') }}" class="@yield('css_classes')">
        <div id="dashboard" v-cloak>
            @include ('layout.user-bar')

            <div class="d-flex">
                @include ('layout.dashboard-menu')

                <main id="main" class="db-main" :class="{ 'db-main--has-menu' : menuIsVisible }" role="main">

                    @yield('content')

                </main>
            </div>
        </div>

        @includeWhen(view()->hasSection('has_gallery'), 'components.gallery')

        <!-- Scripts -->
        <script src="{{ version('js/vendor.js') }}"></script>
        <script src="{{ version('js/admin.js') }}"></script>
        @yield('scripts')
    </body>
</html>
