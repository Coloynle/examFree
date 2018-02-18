@extends('layouts.iframe')
@section('content')
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
    <script>
        /* Ueditor创建 */
        function getUeditor(id){
            var ue = UE.getEditor(id,{
                initialFrameWidth : '100%'
            });
        }

        /* 初始化按钮 */
        function initRadio(){
            $('.panel-header input').iCheck({
                checkboxClass: 'icheckbox-grey',
                radioClass: 'iradio-grey t-2',
                increaseArea: '20%'
            })
        }

        /* 优化用户体验 点击整条任意地方都能切换选择 */
        function checkRadio(that) {
            $(that).find('.radio-box').eq(0).iCheck('toggle');
        }

        /* 添加一个选项 */
        function addOptions(divObj) {
            var divHtml = "<div class=\"panel panel-secondary\" data-content=\"E\" id=\"option_E\">\n" +
                                "<div class=\"panel-header\" onclick=\"checkRadio(this);\">\n" +
                                    "<div class=\"radio-box\">\n" +
                                        "<input type=\"radio\" id=\"option_radio_E\" name=\"option_radio\">\n" +
                                        "<label for=\"option_radio_E\">E</label>\n" +
                                    "</div>" +
                                "</div>" +
                                "<div class=\"panel-body\">\n" +
                                    "<script id=\"option_content_E\" class=\"ueditor\" name=\"option_E\" type=\"text/plain\"><\/script>"+
                                "</div>"+
                           "</div>";
            $(divHtml).appendTo($(divObj));
            getUeditor('option_content_E');
            initRadio();
            console.log($(divHtml));
        }
    </script>
    {{-- 试题类型 --}}
    <div class="HuiTab">
        <div class="tabBar clearfix">
            @foreach(config('exam.question_type') as $item => $value)
                <span
                      @if($item == $context['status']['type'])
                      class="current"
                      @endif
                      @if(!$context['status']['id'])
                      onclick="location.href ='{{url('admin/question/addQuestion/'.$item)}}'"
                      @endif
                >{{ $value }}</span>
                @endforeach
        </div>
    </div>
    {{-- 试题类型END --}}
    <div class="page-container">
        <form class="form form-horizontal" method="POST" action="">
            {{ csrf_field() }}
            <input type="hidden" value="{{ $context['status']['type'] }}" id="questionType">
            @if( $context['status']['type'] == 'SingleChoice')
                <div class="panel panel-secondary">
                    <div class="panel-header">试题描述</div>
                    <div class="panel-body">
                        <script id="description" name="description" type="text/plain"></script>
                    </div>
                </div>
                <div class="mt-20" id="option">
                    <div class="panel panel-secondary" data-content="A" id="option_A">
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_A" name="option_radio">
                                <label for="option_radio_A">A</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_A" class="ueditor" name="option_A" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="panel panel-secondary" data-content="B" id="option_A">
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_B" name="option_radio">
                                <label for="option_radio_B">B</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_B" name="option_B" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="panel panel-secondary" data-content="C" id="option_C">
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_C" name="option_radio">
                                <label for="option_radio_C">C</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_C" name="option_C" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="panel panel-secondary" data-content="D" id="option_D">
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_D" name="option_radio">
                                <label for="option_radio_D">D</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_D" name="option_D" type="text/plain"></script>
                        </div>
                    </div>
                </div>
                <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
                <script>
                    $(function(){
                        getUeditor('description');
                        getUeditor('option_content_A');
                        getUeditor('option_content_B');
                        getUeditor('option_content_C');
                        getUeditor('option_content_D');
                        initRadio();
                    });
                </script>
            @elseif(  $context['status']['type'] == 'MultipleChoice' )
            @elseif(  $context['status']['type'] == 'TrueOrFalse' )
            @elseif(  $context['status']['type'] == 'FillInTheBlank' )
            @elseif(  $context['status']['type'] == 'ShortAnswer' )
            @else
                <div class="flex-center position-ref full-height">
                    <div class="content">
                        <div class="title">
                            Sorry, the page you are looking for could not be found.
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection
