<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
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

    <link href="{{ asset('h-ui/static/h-ui/css/H-ui.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('h-ui/static/h-ui.admin/css/H-ui.admin.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('h-ui/lib/Hui-iconfont/1.0.8/iconfont.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('h-ui/static/h-ui.admin/skin/blue/skin.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('h-ui/lib/icheck/icheck.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>

    <!--[if IE 6]>
    <script type="text/javascript" src="{{ asset('h-ui/lib/DD_belatedPNG_0.0.8a-min.js') }}"></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
</head>
<body>

<div class="page-container">
    @foreach($question[0] as $item => $value)
        @if($item == 'description' || $item == 'analysis')
            <div class="panel panel-secondary-change mt-20 mb-20 radius">
                @if($item == 'description')
                <div class="panel-header">试题描述</div>
                @else
                <div class="panel-header">试题讲解</div>
                @endif
                <div class="panel-body" id="{{ $item }}">{!! $value !!}</div>
            </div>
        @elseif($item == 'answer_info' && $question[0]['type'] != 'FillInTheBlank')
            @foreach($value as $ite => $val)
                @if(($question[0]['type'] == 'MultipleChoice' && in_array($ite,$question[0]['answer'])) || (($question[0]['type'] == 'SingleChoice' || $question[0]['type'] == 'TrueOrFalse') && $ite == $question[0]['answer']))
                    {{-- 如果选项是正确答案 --}}
                    <div class="panel panel-success radius">
                        <div class="panel-header">{{ $ite }}</div>
                        <div class="panel-body">{!! $val !!}</div>
                    </div>
                @else
                    {{-- 如果不是正确答案（简答题不给颜色） --}}
                    <div class="panel panel-secondary-change radius">
                        <div class="panel-header">{{ $ite }}</div>
                        <div class="panel-body">{!! $val !!}</div>
                    </div>
                @endif
            @endforeach
        @elseif($question[0]['type'] == 'FillInTheBlank' && $item == 'answer_info')
            @foreach($value as $ite => $val)
            <input type="hidden" id="option_answer_info_{{ $ite }}" value="{{ $val }}">
            @endforeach
        @endif
    @endforeach
</div>
<script>
    $(function () {
       $('#description input').each(function () {
           $(this).attr('type','text');
           $(this).val($('#option_answer_info_'+$(this).attr('class')).val());
           $(this).attr('class','input-text');
           $(this).attr('disabled','true');
           $(this).attr('style','width:200px;margin:0 5px')
       })
    });
</script>

{{--<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>--}}
<script type="text/javascript" src="{{ asset('h-ui/lib/layer/2.4/layer.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.validation/1.14.0/messages_zh.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/static/h-ui.admin/js/H-ui.admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('h-ui/lib/jquery.contextmenu/jquery.contextmenu.r2.js') }}"></script>
</body>
</html>