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

    <title>{{ config('app.admin','ExamAdmin').' - Login' }}</title>

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

{{-- 头部文件模板 --}}
<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl">
            <a class="logo navbar-logo f-l mr-10 hidden-xs" href="{{ url('/admin') }}">{{ config('app.admin', 'ExamAdmin') }}</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.0</span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
        </div>
    </div>
</header>
<div class="container mt-50 col-sm-5 f-clean">
    <div class="row">
        <div>
            <div class="panel panel-default">
                <div class="panel-header">Login</div>

                <div class="panel-body">
                    <form class="form form-horizontal" method="POST" action="{{ url('/admin/login') }}">
                        {{ csrf_field() }}

                        <div class="row cl{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="form-label col-xs-5 col-sm-5">E-Mail Address</label>
                            <div class="formControls col-xs-5 col-sm-5">
                                <input id="email" type="email" class="input-text radius" name="email" value="{{ old('email') }}" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row cl{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="form-label col-xs-5 col-sm-5">Password</label>

                            <div class="formControls col-xs-5 col-sm-5">
                                <input id="password" type="password" class="input-text radius" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row cl">
                            <div class="col-xs-2 col-sm-3 col-md-offset-4 skin-minimal">
                                <div class="check-box">
                                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember">Remember Me</label>
                                </div>
                            </div>
                            <div class="">
                                <button type="submit" class="btn btn-primary radius">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('h-ui/lib/jquery/1.9.1/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/layer/2.4/layer.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/messages_zh.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui.admin/js/H-ui.admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.contextmenu/jquery.contextmenu.r2.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/icheck/jquery.icheck.min.js')}}"></script>

<script>
    $(function(){
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });
    });
</script>
</body>
</html>
