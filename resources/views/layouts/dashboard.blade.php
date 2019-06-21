@php $substituteTitle = $substituteTitle ?? false @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ (isset($pageTitle) ? ($pageTitle . ($substituteTitle ? '' : ' | ')) : '') . ($substituteTitle ? '' : config('app.name', 'Laravel')) }}</title>
    @if(isset($pageDescription))
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @include('layouts.partials.favicon')

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            margin-bottom: 55px
        }
        footer{
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 55px;
        }
    </style>
    @stack('additional-styles')
</head>
<body>
<div id="app">
    @include('layouts.partials.menu')

    <main class="py-4">
        @include('flash::message')
        @yield('content')
    </main>
    <footer class="footer">
        @include('layouts.partials.footer')
    </footer>
</div>
@include('layouts.partials.modals')
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script>
    jQuery('#flash-overlay-modal').modal();
</script>
@stack('before-body-ends')
</body>
</html>
