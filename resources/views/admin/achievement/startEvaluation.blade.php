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
    <div id="evaluation">
        @foreach($question as $questionn)
            @foreach($questionn as $item => $value)
                @if($item == 'description' || $item == 'analysis')
                    <div class="panel panel-secondary-change mt-20 mb-20 radius">
                        @if($item == 'description')
                            <div class="panel-header">试题描述</div>
                        @else
                            <div class="panel-header">试题讲解</div>
                        @endif
                        <div class="panel-body" id="{{ $item }}">{!! $value !!}</div>
                    </div>
                @elseif($item == 'answer_info')
                    @foreach($value as $ite => $val)
                        <div class="panel panel-secondary-change radius">
                            <div class="panel-header">学生答案{{ $ite }}</div>
                            <div class="panel-body">{!! $questionn['user_answer'][$ite] !!}</div>
                            <div class="panel-header">正确答案{{ $ite }}</div>
                            <div class="panel-body">{!! $val !!}</div>
                        </div>
                    @endforeach
                @elseif($item == 'score')
                    <input type="number" max="{{ $value }}" name="score[{{ $questionn['id'] }}]" class="input-text radius f-l mr-10 mt-10" style="width: 100px" placeholder="分数">
                    <span class="mt-35" style="display: block">满分：{{ $value }}分</span>
                @endif
            @endforeach
        @endforeach
    </div>
    <input id="saveExam" type="button" class="btn btn-success-outline radius btn-block mt-10" value="保存批卷" onclick="saveEvaluation(this)">
</div>

<script>

    $(function () {

        //AJAX TOKEN初始化
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    //保存试卷
    function saveEvaluation(that) {
        var flag = false;
        var data = {
            'score' : 0
        };
        $('input[type=number]').each(function () {
            if($(this).val() == ''){
                $.Huimodalalert('必须对所有试题进行判分', 1500);
                flag = true;
                return false;
            }
            data['score'] += parseInt($(this).val());
        });

        if(flag){
            return false;
        }

        $.ajax({
            'type': 'POST',
            'data': data,
            'url': '{{ url('admin/achievement/saveEvaluation/'.$id) }}',
            'beforeSend': function () {
                $(that).attr('disabled', 'disabled');
            },
            'success': function (data) {
                $.Huimodalalert(data.message, 1500);
                setTimeout(function () {
                    if(data.code == 1){
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index)
                    }
                    window.location.reload();
                }, 1500);
            }
        })
    }
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