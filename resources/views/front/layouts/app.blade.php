<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Roboto:400,500,700&display=swap"
          rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.17/vue.min.js"></script>

    <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
    </script>

    @stack('field_style')
    @stack('owl')
    <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"
          rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('svg/icons/svg-symbols.css')}}">
    <link href="{{asset('css/style.min.css')}}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('js/js.js')}}"></script>


<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description')">
    <title>@yield('meta_title')</title>
</head>
<body>
@include('front.layouts.pre-header')
@include('front.layouts.header')
@yield('content')
@include('front.layouts.' . $common_data->footer_template)
{{--@include('front.layouts.footer')--}}
</body>
<!-- Scripts -->
<script src="{{ asset('js/main.js') }}" defer></script>
<script src="{{ asset('js/order.js') }}" defer></script>
<script src="{{ asset('js/auth.js') }}" defer></script>
</html>
