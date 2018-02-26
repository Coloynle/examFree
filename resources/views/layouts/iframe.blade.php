<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('ADMIN_NAME','ExamAdmin') }}</title>
    <script type="text/javascript" src="{{ asset('h-ui/lib/jquery/1.9.1/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('h-ui/lib/icheck/jquery.icheck.min.js')}}"></script>
    <!-- Styles -->
    {{--<link rel="Bookmark" href="/favicon.ico" >--}}
    {{--<link rel="Shortcut Icon" href="/favicon.ico" />--}}
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('h-ui/lib/html5shiv.js')}}"></script>
    <script type="text/javascript" src="{{ asset('h-ui/lib/respond.min.js')}}"></script>

    <![endif]-->

    <link href="{{ asset('h-ui/static/h-ui/css/H-ui.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('h-ui/static/h-ui.admin/css/H-ui.admin.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('h-ui/lib/Hui-iconfont/1.0.8/iconfont.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('h-ui/static/h-ui.admin/skin/blue/skin.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('h-ui/lib/icheck/icheck.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css" />

    <!--[if IE 6]>
    <script type="text/javascript" src="{{ asset('h-ui/lib/DD_belatedPNG_0.0.8a-min.js') }}" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
</head>
<body>

{{-- 刷新栏 --}}
<nav class="breadcrumb">
    <i class="Hui-iconfont">&#xe67f;</i>
    {{--循环通过配置文件获取目录位置--}}
    {{-- 做一个标识第一个不显示 > --}}
    <?php $first = false; ?>
    @foreach(explode('/',Request::getPathInfo()) as $key => $val)
        @if(config('exam.list_name.'.$val) or false)
            @if( $first )
                <span class="c-gray en">&gt;</span>
            @endif
            {{ config('exam.list_name.'.$val,'error') }}
            <?php $first = true; ?>
        @endif
    @endforeach

    <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" >
        <i class="Hui-iconfont">&#xe68f;</i>
    </a>
</nav>
{{-- 刷新栏END --}}

@yield('content')
<script type="text/javascript" src="{{ asset('h-ui/lib/layer/2.4/layer.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/messages_zh.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui.admin/js/H-ui.admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.contextmenu/jquery.contextmenu.r2.js') }}"></script>
</body>
</html>
