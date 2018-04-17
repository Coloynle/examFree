<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ExamFree').' - '.$exam['name'] }}</title>
    <script type="text/javascript" src="{{ asset('h-ui/lib/jquery/1.9.1/jquery.js') }}"></script>

    <link href="{{ asset('uikit/css/uikit.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('uikit/css/uikit.almost-flat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('uikit/css/uikit.gradient.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('uikit/css/components/form-advanced.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('uikit/css/components/sticky.css') }}" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="{{ asset('uikit/js/uikit.js')}}"></script>
    <script type="text/javascript" src="{{ asset('uikit/js/components/sticky.js')}}"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>
</head>
<script>

</script>
<body>
<div>
    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
