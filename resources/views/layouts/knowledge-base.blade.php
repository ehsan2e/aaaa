@php
    $knowledgeBaseTitle = ($knowledgeBaseTitle ?? trans('knowledge-base.title')) . ' | '. config('app.name', 'Laravel');
    $knowledgeBaseDescription = $knowledgeBaseDescription ?? trans('knowledge-base.description');
    $knowledgeBaseCanonicalUrl = $knowledgeBaseCanonicalUrl ?? \Illuminate\Support\Facades\Request::fullUrl();
    \App\Facades\UIManager::setActivePath('knowledgebase');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $knowledgeBaseTitle }}</title>
    <meta name="description" content="{{ $knowledgeBaseDescription }}">
    @if(isset($knowledgeBaseKeywords))
        <meta name="keywords" content="{{ $knowledgeBaseKeywords }}">
    @endif
    @include('layouts.partials.favicon')

    @yield('meta')

    <link rel="canonical" href="{{ $knowledgeBaseCanonicalUrl }}">

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

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">{{ __('Categories') }}</div>
                        <div class="card-body">
                            @yield('menu')
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>

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