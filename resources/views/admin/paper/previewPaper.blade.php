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

    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
</head>
<body>

<div class="page-container">
    @foreach($paper as $item => $value)
        @if($item == 'name')
            <h2 class="text-c">{{ $value }}</h2>
        @elseif($item == 'passing_score')
            <span class="mr-10">及格分数：{{ $value }}</span>
        @elseif($item == 'total_score')
            <span class="mr-10">试卷总分：{{ $value }}</span>
        @elseif($item == 'type')
            <span class="mr-10">试卷分类：{{ $value }}</span>
        @elseif($item == 'content')
            @foreach($value as $key => $val)
                <div class="panel panel-secondary-change mt-10">
                    <div class="panel-header cl">
                        {{ $key }}
                    </div>
                    <div class="panel-body">
                        {{-- 小题题号 --}}
                        <?php $questionNum=1; ?>
                        {{-- 遍历小题信息 --}}
                        @foreach( $val as $questionId => $content)
                            <div class="panel-body">
                                <div class="cl">
                                    <span class="badge badge-secondary radius mt-5" name="questionNum"><?php echo $questionNum++; ?></span>
                                    <span class="label label-default radius mt-5 ml-10">{{ $content['type'] }}</span>
                                    <span class="f-r mr-15">{{ $content['score'] }}分</span>
                                </div>
                                <div class="panel-body">
                                    <div class="description" data-questionId="{{ $questionId }}">
                                        {!! $content['description'] !!}
                                    </div>
                                    <div>
                                        @if($content['type'] == '单选题' || $content['type'] == '判断题')
                                            @foreach($content['answer_info'] as $option => $detail)
                                                <div class="skin-minimal">
                                                    <div class="radio-box" style="display: block">
                                                        <label>
                                                            <input class="radio" type="radio" name="{{ $questionId }}">
                                                            <p class="f-l">{{ $option }}.</p>
                                                            {!! $detail !!}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif($content['type'] == '多选题')
                                            @foreach($content['answer_info'] as $option => $detail)
                                            <div class="skin-minimal">
                                                <div class="check-box" style="display: block">
                                                    <label>
                                                        <input type="checkbox" name="{{ $questionId }}[{{ $option }}]">
                                                        <p class="f-l">{{ $option }}.</p>
                                                        {!! $detail !!}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        {{--
                                        @elseif($content['type'] == '填空题')
                                            @foreach($content['answer_info'] as $option => $detail)
                                                <input type="hidden" id="{{$questionId}}[{{ $option }}]" value="{{ $detail }}">
                                            @endforeach
                                            --}}
                                        @elseif($content['type'] == '简答题')
                                            @foreach($content['answer_info'] as $option => $detail)
                                            <div onload="">
                                                <script id="{{$questionId}}[{{ $option }}]" name="{{$questionId}}[{{ $option }}]" type="text/plain"></script>
                                            </div>
                                            <script>
                                                $(function () {
                                                    getUeditor('{{$questionId}}[{{ $option }}]', '');
                                                });
                                            </script>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach
</div>
<script>
    $(function () {
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-grey t-2',
            radioClass: 'iradio-grey t-2',
            increaseArea: '20%'
        });

        $('.description input').each(function () {
            $(this).attr('type','text');
            // $(this).val($('#option_answer_info_'+$(this).data('content')).val());
            $(this).attr('class','input-text');
            $(this).attr('value','');
            $(this).attr('name',$(this).parents('.description').data('questionid')+'['+ $(this).data('content') +']');
            // $(this).attr('disabled','true');
            $(this).attr('style','width:200px;margin:0 5px')
        })
    });

    /* Ueditor创建 */
    function getUeditor(id, content) {
        content = content || '';
        var ue = UE.getEditor(id, {
            initialFrameWidth: '100%'
        });
        //防止提交表单时，不提交没有使用过的编辑器
        ue.addListener("ready", function () {
            // editor准备好之后才可以使用
            ue.setContent(content);
        });
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