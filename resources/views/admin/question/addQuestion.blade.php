@extends('layouts.iframe')
@section('content')
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
    <script>

        /* 全局变量 */
        var type = 'radio';
        var typeClass = 'radio-box';
        var delButton = "<a class=\"btn btn-danger radius f-r btn-add\" href=\"javascript:;\" onclick=\"deleteOption(this)\">" +
            "<i class=\"Hui-iconfont Hui-iconfont-del3\"></i>" +
            "</a>";


        /* Ueditor创建 */
        function getUeditor(id,content){
            content = content || '';
            var ue = UE.getEditor(id,{
                initialFrameWidth : '100%'
            });
            //防止提交表单时，不提交没有使用过的编辑器
            ue.addListener("ready", function () {
                // editor准备好之后才可以使用
                ue.setContent(content);
            });
        }

        /* 初始化按钮 */
        function initRadio(){
            $('.panel-header input').iCheck({
                checkboxClass: 'icheckbox-grey t-2',
                radioClass: 'iradio-grey t-2',
                increaseArea: '20%'
            })
        }

        /* 优化用户体验 点击整条任意地方都能切换选择 */
        function checkRadio(that) {
            $(that).find('.'+typeClass).eq(0).iCheck('toggle');
        }

        /* 添加一个选项 */
        function addOptions(divObj) {
            var code = $(divObj).find('.panel-secondary-change').last().data('content');
            if(code.charCodeAt(0) >= 90 || code.charCodeAt(0) < 65){
                $.Huimodalalert('选项数量到达上限',2000);
                return false;
            }else{
                var word = String.fromCharCode(code.charCodeAt(0) + 1);
            }
            var divHtml = "<div class=\"panel panel-secondary-change\" data-content=\""+ word +"\" id=\"option_"+ word +"\">" +
                                "" + delButton + "" +
                                "<div class=\"panel-header\" onclick=\"checkRadio(this);\">" +
                                    "<div class=\""+ typeClass +"\">\n" +
                                        "<input type=\""+ type +"\" id=\"option_"+ type +"_"+ word +"\" name=\"option_"+ type +"\"  value=\""+ word +"\">\n" +
                                        "<label for=\"option_"+ type +"_"+ word +"\">"+ word +"</label>" +
                                    "</div>" +
                                "</div>" +
                                "<div class=\"panel-body\">" +
                                    "<script id=\"option_content_"+ word +"\" class=\"ueditor\" name=\"option["+ word +"]\" type=\"text/plain\"><\/script>"+
                                "</div>"+
                           "</div>";
            $(divHtml).appendTo($(divObj));
            getUeditor('option_content_'+word);
            initRadio();
        }

        /* 删除一个选项 */
        function deleteOption(that) {
            if(!singleChoiceCount()){
                return false;
            }
            var option = $(that).parent().attr('id');
            var startCode = $(that).parent().data('content');
            var endCode = $('#option').find('.panel-secondary-change').last().data('content');
            $('#model_true').attr('onclick','resetCode("'+option+'","'+startCode+'","'+endCode+'")');
            $("#modal").modal("show");
        }

        /* 内容转移删除最后一个Ueditor */
        function resetCode(option,startCode,endCode) {
            var asciiCode = startCode.charCodeAt(0);
            var flag = false;
            if(startCode != endCode) {
                $('.ueditor').each(function () {
                    var that = $(this);
                    that.data('content', String.fromCharCode(asciiCode));
                    var id = that.attr('id');
                    if (id == 'option_content_' + startCode) {
                        flag = true;
                    } else if (id == 'option_content_' + endCode) {
                        flag = false;
                    }
                    if (flag) {
                        var nextUeditorContent = UE.getEditor('option_content_' + String.fromCharCode(asciiCode + 1)).getContent();
                        UE.getEditor(id).setContent(nextUeditorContent);
                        asciiCode++;
                    }
                });
            }
            $('#option_'+endCode).remove();
            UE.getEditor('option_content_'+endCode).destroy();
            $('[name=option_'+endCode+']').remove();
            $('#modal').modal('hide');
        }

        /* 判断单选数量保证至少有三个选项 */
        function singleChoiceCount(){
            var count = $('#option').find('.panel-secondary-change').length;
            if(count == 3){
                $.Huimodalalert('选项数量不可小于3个',2000);
                return false;
            }else{
                return true;
            }
        }

        /* 初始化选项 */
        function initOption(length) {
            //默认为4个选项
            length = length || 69;
            var divObj = '#option';
            for(var i=65;i<length;i++){
                var word = String.fromCharCode(i);
                var divHtml = "<div class=\"panel panel-secondary-change\" data-content=\""+ word +"\" id=\"option_"+ word +"\">" +
                    "" + delButton + "" +
                        "<div class=\"panel-header\" onclick=\"checkRadio(this);\">" +
                    "<div class=\""+ typeClass +"\">" +
                    "<input type=\""+ type +"\" id=\"option_"+ type +"_"+ word +"\" name=\"option_"+ type +"\"  value=\""+ word +"\">" +
                    "<label for=\"option_"+ type +"_"+ word +"\">"+ word +"</label>" +
                    "<span class=\"ml-50 c-error\" id=\"option_error_"+ word +"\"></span>" +
                    "<span class=\"ml-50 c-error\" id=\"option_error\"></span>" +
                    "</div>" +
                    "</div>" +
                    "<div class=\"panel-body\">" +
                    "<script id=\"option_content_"+ word +"\" class=\"ueditor\" name=\"option["+ word +"]\" type=\"text/plain\"><\/script>"+
                    "</div>"+
                    "</div>";
                $(divHtml).appendTo($(divObj));
            }
            initRadio();
        }

    </script>

    @foreach($errors->all() as $item => $error)
    {{ $item .':'. $error.'<br/>' }}
    @endforeach
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
        <form class="form form-horizontal" method="POST" action="{{ url('admin/question/createQuestion') }}">
            {{ csrf_field() }}
            <input type="hidden" value="{{ $context['status']['type'] }}" name="questionType" id="questionType">
            <input type="hidden" value="{{ $context['length'] }}" name="length" id="length">
            <div class="panel panel-secondary-change">
                <div class="panel-header">
                    <span>试题描述</span>
                    <span class="ml-40 c-error">{{ $errors->first('description') }}</span>
                </div>
                <div class="panel-body">
                    <script id="description" name="description" type="text/plain"></script>
                </div>
            </div>
            @if( $context['status']['type'] == 'SingleChoice')
                <div class="mt-20" id="option">

                    {{--<div class="panel panel-secondary-change" data-content="A" id="option_A">--}}
                        {{--<a class="btn btn-danger radius f-r btn-add" href="javascript:;" onclick="deleteOption(this)">--}}
                            {{--<i class="Hui-iconfont Hui-iconfont-del3"></i>--}}
                        {{--</a>--}}
                        {{--<div class="panel-header" onclick="checkRadio(this);">--}}
                            {{--<div class="radio-box">--}}
                                {{--<input type="radio" id="option_radio_A" checked name="option_radio" value="A">--}}
                                {{--<label for="option_radio_A">A</label>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<script id="option_content_A" class="ueditor" name="option[A]" type="text/plain"></script>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="panel panel-secondary-change" data-content="B" id="option_B">--}}
                        {{--<a class="btn btn-danger radius f-r btn-add" href="javascript:;" onclick="deleteOption(this)">--}}
                            {{--<i class="Hui-iconfont Hui-iconfont-del3"></i>--}}
                        {{--</a>--}}
                        {{--<div class="panel-header" onclick="checkRadio(this);">--}}
                            {{--<div class="radio-box">--}}
                                {{--<label for="option_radio_B">B</label>--}}
                                {{--<input type="radio" id="option_radio_B" name="option_radio" value="B">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<script id="option_content_B" name="option[B]" class="ueditor" type="text/plain"></script>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="panel panel-secondary-change" data-content="C" id="option_C">--}}
                        {{--<a class="btn btn-danger radius f-r btn-add" href="javascript:;" onclick="deleteOption(this)">--}}
                            {{--<i class="Hui-iconfont Hui-iconfont-del3"></i>--}}
                        {{--</a>--}}

                        {{--<div class="panel-header" onclick="checkRadio(this);">--}}
                            {{--<div class="radio-box">--}}
                                {{--<input type="radio" id="option_radio_C" name="option_radio" value="C">--}}
                                {{--<label for="option_radio_C">C</label>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<script id="option_content_C" name="option[C]" class="ueditor" type="text/plain"></script>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="panel panel-secondary-change" data-content="D" id="option_D">--}}
                        {{--<a class="btn btn-danger radius f-r btn-add" href="javascript:;" onclick="deleteOption(this)">--}}
                            {{--<i class="Hui-iconfont Hui-iconfont-del3"></i>--}}
                        {{--</a>--}}
                        {{--<div class="panel-header" onclick="checkRadio(this);">--}}
                            {{--<div class="radio-box">--}}
                                {{--<input type="radio" id="option_radio_D" name="option_radio" value="D">--}}
                                {{--<label for="option_radio_D">D</label>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<script id="option_content_D" name="option[D]" class="ueditor" type="text/plain"></script>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
                <script>
                    $(function(){
                        getUeditor('description','{!! old('description') !!}');
                        getUeditor('analysis','{!! old('description') !!}');
                        var length = parseInt('{{ $context['length'] }}') || 4;
                        initOption(65+length);
                        @for($i=65;$i<$context['length']+65;$i++)
                        getUeditor('option_content_' + '{{ chr($i) }}', '{!! old('option')[chr($i)] !!}');
                        $('#option_error_'+'{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                        @endfor
                        $('#option_error').html('{{ $errors->first('option_radio' }}');
                        // for(var i=65;i<65+length;i++) {
                        //     var word = String.fromCharCode(i);
                        // }
                        // getUeditor('option_content_A');
                        // getUeditor('option_content_B');
                        // getUeditor('option_content_C');
                        // getUeditor('option_content_D');
                        // initRadio();
                    });
                </script>
                <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content radius">
                            <div class="modal-header">
                                <h3 class="modal-title">警告</h3>
                                <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:;">×</a>
                            </div>
                            <div class="modal-body">
                                <p>确定要删除此选项吗？此操作不可撤回</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="button" id="model_true">确定</button>
                                <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
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
            <div class="panel panel-secondary-change mt-10">
                <div class="panel-header">
                    <span>试题讲解</span>
                    <span class="ml-40 c-error">{{ $errors->first('analysis') }}</span>
                </div>
                <div class="panel-body">
                    <script id="analysis" name="analysis" type="text/plain"></script>
                </div>
            </div>
            <input id="saveQuestion" type="submit" class="btn btn-success radius btn-block mt-10" value="保存试题">
        </form>
    </div>
@endsection
