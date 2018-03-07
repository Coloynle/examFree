@extends('layouts.iframe')
@section('content')
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
    <script>

        @if(Session::has('message'))
        $(function () {
            $.Huimodalalert('{{ Session::get('message') }}', 2000);
            @if(Session::get('code') == 2)
            var index = parent.layer.getFrameIndex(window.name);
            setTimeout(function(){parent.layer.close(index)}, 2000);
            @endif
        });
        @endif

        /* 全局变量 */
                @if($context['status']['type'] == 'SingleChoice')               {{-- 单选题 --}}
        var type = 'radio';
        var typeClass = 'radio-box';
        var delButton = "<a class=\"btn btn-danger radius f-r btn-add\" href=\"javascript:;\" onclick=\"deleteOption(this)\">" +
            "<i class=\"Hui-iconfont Hui-iconfont-del3\"></i>" +
            "</a>";
                @elseif(  $context['status']['type'] == 'MultipleChoice' )      {{-- 多选题 --}}
        var type = 'checkbox';
        var typeClass = 'check-box';
        var delButton = "<a class=\"btn btn-danger radius f-r btn-add\" href=\"javascript:;\" onclick=\"deleteOption(this)\">" +
            "<i class=\"Hui-iconfont Hui-iconfont-del3\"></i>" +
            "</a>";
                @elseif(  $context['status']['type'] == 'TrueOrFalse' )         {{-- 判断题 --}}
        var type = 'radio';
        var typeClass = 'radio-box';
        var delButton = "";
                @elseif(  $context['status']['type'] == 'FillInTheBlank' )      {{-- 填空题 --}}
        var type = '';
        var typeClass = '';
                @elseif(  $context['status']['type'] == 'ShortAnswer' )         {{-- 简答题 --}}
        var type = '';
        var typeClass = '';
        var delButton = "<a class=\"btn btn-danger radius f-r btn-add\" href=\"javascript:;\" onclick=\"deleteOption(this)\">" +
            "<i class=\"Hui-iconfont Hui-iconfont-del3\"></i>" +
            "</a>";
        @else                                                           {{-- 抛出异常 --}}
        @endif

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

        /* 初始化按钮 */
        function initRadio() {
            $('.panel-header input').iCheck({
                checkboxClass: 'icheckbox-grey t-2',
                radioClass: 'iradio-grey t-2',
                increaseArea: '20%'
            })
        }

        /* 优化用户体验 点击整条任意地方都能切换选择 */
        function checkRadio(that) {
            $(that).find('.' + typeClass).eq(0).iCheck('toggle');
        }

        /* 添加一个选项 */
        function addOptions(divObj) {
            @if($context['status']['type'] == 'FillInTheBlank')
            var code = $(divObj).find('.btn-group').last().data('content') || '@';
            @else
            var code = $(divObj).find('.panel-secondary-change').last().data('content');
            @endif
            if (code.charCodeAt(0) >= 90 || code.charCodeAt(0) < 64) {
                $.Huimodalalert('选项数量到达上限', 2000);
                return false;
            } else {
                var word = String.fromCharCode(code.charCodeAt(0) + 1);
            }
            @if($context['status']['type'] == 'FillInTheBlank')
            var divHtml = "<div id=\"option_content_"+ word +"\" class=\"btn-group cl mb-10 f-l mr-20\" data-content='"+ word +"'>" +
                            "<a class=\"btn btn-secondary radius f-l\" style=\"width: 36px\">"+ word +"</a>" +
                            "<input type=\"text\" name=\"option["+ word +"]\" class=\"input-text f-l\" style=\"width: 300px;\">" +
                            "<a style=\"text-decoration:none\" class=\"btn btn-danger radius f-l\" onClick=\"batchDeletion('del')\" href=\"javascript:;\" title=\"删除\">" +
                              "<i class=\"Hui-iconfont Hui-iconfont-del2\"> </i>" +
                            "</a>" +
                          "</div>";
            $(divHtml).appendTo($(divObj));
            var ue = UE.getEditor('description');
            var option = '<input type="button" data-content="'+ word +'" value="填空'+ word +'" id="option_'+ word +'">'
            ue.focus();
            ue.execCommand('inserthtml',option);

            //遍历编辑器内按钮 判断是否是在中间插入的填空
            var ueObj = $(ue.getContent());
            var tempCode = 65;
            var changeCode = false;
            ueObj.find('input[type=button]').each(function () {
                if(changeCode){
                    $(this).attr('id','option_'+String.fromCharCode(tempCode));
                    $(this).val('填空'+String.fromCharCode(tempCode));
                    $(this).data('content',String.fromCharCode(tempCode));
                }
                else if($(this).data('content') != String.fromCharCode(tempCode)){
                    changeCode = tempCode;
                    $(this).attr('id','option_'+String.fromCharCode(tempCode));
                    $(this).val('填空'+String.fromCharCode(tempCode));
                    $(this).data('content',String.fromCharCode(tempCode));
                }
                tempCode++;
            });
            ue.setContent('');
            var temp = '';
            ueObj.each(function () {
                temp += this.outerHTML;
            });
            ue.setContent(temp);

            //遍历下方填空处顺序
            if(changeCode){
                for($i=word.charCodeAt(0);$i>=changeCode;$i--){
                    if($i==changeCode){
                        $('#option_content_' + String.fromCharCode($i) + ' input').val('');
                    }else {
                        $('#option_content_' + String.fromCharCode($i) + ' input').val($('#option_content_' + String.fromCharCode($i-1) + ' input').val());
                    }
                }
            }
            @else
            var divHtml = "<div class=\"panel panel-secondary-change\" data-content=\"" + word + "\" id=\"option_" + word + "\">" +
                "" + delButton + "" +
                "<div class=\"panel-header\" " +
                @if($context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse' || $context['status']['type'] == 'MultipleChoice')
                "onclick=\"checkRadio(this);" +
                @endif
                "\">" +
                "<div class=\"" + typeClass + "\">\n" +
                @if($context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse' || $context['status']['type'] == 'MultipleChoice')
                "<input type=\"" + type + "\" id=\"option_" + type + "_" + word + "\"" +
                    //单选多选区分
                    @if(  $context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse')
                        "name=\"option_" + type + "\"  value=\"" + word + "\">" +
                    @elseif( $context['status']['type'] == 'MultipleChoice' )
                        "name=\"option_" + type + "[" + word + "]\"  value=\"" + word + "\">" +
                    @endif
                @endif
                "<label for=\"option_" + type + "_" + word + "\">" + word + "</label>" +
                "</div>" +
                "</div>" +
                "<div class=\"panel-body\">" +
                "<script id=\"option_content_" + word + "\" class=\"ueditor\" name=\"option[" + word + "]\" type=\"text/plain\"><\/script>" +
                "</div>" +
                "</div>";
                $(divHtml).appendTo($(divObj));
                getUeditor('option_content_' + word);
                initRadio();
            @endif
        }

        /* 删除一个选项 */
        function deleteOption(that) {
            if (!singleChoiceCount()) {
                return false;
            }
            var option = $(that).parent().attr('id');
            var startCode = $(that).parent().data('content');
            var endCode = $('#option').find('.panel-secondary-change').last().data('content');
            $('#model_true').attr('onclick', 'resetCode("' + option + '","' + startCode + '","' + endCode + '")');
            $("#modal").modal("show");
        }

        /* 内容转移删除最后一个Ueditor */
        function resetCode(option, startCode, endCode) {
            var asciiCode = startCode.charCodeAt(0);
            var flag = false;
            if (startCode != endCode) {
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
            $('#option_' + endCode).remove();
            UE.getEditor('option_content_' + endCode).destroy();
            $('[name=option_' + endCode + ']').remove();
            $('#modal').modal('hide');
        }

        /* 判断单选数量保证至少有三个选项 */
        function singleChoiceCount() {
            var count = $('#option').find('.panel-secondary-change').length;
            @if(  $context['status']['type'] == 'ShortAnswer' )
            var minCount = 1;
            @else
            var minCount = 3;
            @endif
            if(count == minCount){
                $.Huimodalalert('答案数量不可小于'+ minCount +'个', 2000);
                return false;
            } else {
                return true;
            }
        }

        /* 初始化选项 */
        function initOption(length) {
            //默认为4个选项
            length = length || 69;
            var divObj = '#option';
            for (var i = 65; i < length; i++) {
                var word = String.fromCharCode(i);
                var divHtml = "<div class=\"panel panel-secondary-change\" data-content=\"" + word + "\" id=\"option_" + word + "\">" +
                    "" + delButton + "" +
                    "<div class=\"panel-header\" " +
                    @if($context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse' || $context['status']['type'] == 'MultipleChoice')
                    "onclick=\"checkRadio(this);" +
                    @endif
                    "\">" +
                    "<div class=\"" + typeClass + "\">" +
                    @if($context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse' || $context['status']['type'] == 'MultipleChoice')
                    "<input type=\"" + type + "\" id=\"option_" + type + "_" + word + "\" " +
                        //单选多选区分
                        @if(  $context['status']['type'] == 'SingleChoice' || $context['status']['type'] == 'TrueOrFalse')
                            "name=\"option_" + type + "\"  value=\"" + word + "\">" +
                        @elseif( $context['status']['type'] == 'MultipleChoice' )
                            "name=\"option_" + type + "[" + word + "]\"  value=\"" + word + "\">" +
                        @endif
                    @endif
                            "<label for=\"option_" + type + "_" + word + "\">" + word + "</label>" +
                    "<span class=\"ml-50 c-error\" id=\"option_error_" + word + "\"></span>" +
                    "<span class=\"ml-50 c-error\" id=\"option_error\"></span>" +
                    "</div>" +
                    "</div>" +
                    "<div class=\"panel-body\">" +
                    "<script id=\"option_content_" + word + "\" class=\"ueditor\" name=\"option[" + word + "]\" type=\"text/plain\"><\/script>" +
                    "</div>" +
                    "</div>";
                $(divHtml).appendTo($(divObj));
            }
            initRadio();
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
        <form class="form form-horizontal" method="POST" action="{{ url('admin/question/createQuestion') }}">
            {{ csrf_field() }}
            <input type="hidden" value="{{ $context['status']['type'] }}" name="questionType" id="questionType">
            <input type="hidden" value="{{ $context['status']['id'] }}" name="questionId" id="questionId">
            {{--<input type="hidden" value="{{ $context['length'] }}" name="length" id="length">--}}
            <div class="panel panel-secondary-change">
                <div class="panel-header">
                    <span>试题描述</span>
                    <span class="ml-40 c-error">{{ $errors->first('description') }}</span>
                </div>
                <div class="panel-body">
                    <script id="description" name="description" type="text/plain"></script>
                </div>
            </div>
            @if( $context['status']['type'] == 'SingleChoice')      {{-- 单选题 --}}
            <div class="mt-20" id="option">
                {{-- 单选题选项 --}}
            </div>
            <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
            <script>
                $(function () {
                    getUeditor('description', '{!! old('description') !!}');
                    getUeditor('analysis', '{!! old('analysis') !!}');
                    @if(Session::has('_old_input'))
                    var length = parseInt('{{ count(Session::get('_old_input')['option']) }}');
                    @else
                    var length = 4;
                    @endif
                    initOption(65 + length);
                    @for($i=65;$i<(isset(Session::get('_old_input')['option']) ? count(Session::get('_old_input')['option']) : 4) +65;$i++)
                    getUeditor('option_content_' + '{{ chr($i) }}', '{!! old('option')[chr($i)] !!}');
                    $('#option_error_' + '{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                    @endfor
                    $('#option_error').html('{{ $errors->first('option_radio') }}');
                    $('#option_{{ old('option_radio') }}').find('div').eq(0).click();
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
            @elseif(  $context['status']['type'] == 'MultipleChoice' )      {{-- 多选题 --}}
            <div class="mt-20" id="option">
                {{-- 多选题选项 --}}
            </div>
            <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
            <script>
                $(function () {
                    getUeditor('description', '{!! old('description') !!}');
                    getUeditor('analysis', '{!! old('analysis') !!}');
                    @if(Session::has('_old_input'))
                    var length = parseInt('{{ count(old('option')) }}');
                    @else
                    var length = 4;
                    @endif
                    initOption(65 + length);
                    @for($i=65;$i<( null !== old('option') ? count(old('option')) : 4) +65;$i++)
                    getUeditor('option_content_' + '{{ chr($i) }}', '{!! old('option')[chr($i)] !!}');
                    $('#option_error_' + '{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                    @endfor
                    $('#option_error').html('{{ $errors->first('option_checkbox') }}');
                    @if( null != old('option_checkbox'))
                        @foreach( old('option_checkbox') as $item => $value)
                        $('#option_{{ $item }}').find('div').eq(0).click();
                        @endforeach
                    @endif
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
            @elseif(  $context['status']['type'] == 'TrueOrFalse' )         {{-- 判断题 --}}
            <div class="mt-20 cl" id="option">
                {{-- 判断题选项 --}}
            </div>
            <script>
                $(function () {
                    getUeditor('description', '{!! old('description') !!}');
                    getUeditor('analysis', '{!! old('analysis') !!}');
                    @if(Session::has('_old_input'))
                    var length = parseInt('{{ count(old('option')) }}');
                    @else
                    var length = 0;
                    @endif
                    initOption(65 + length);
                    @for($i=65;$i<67;$i++)
                    getUeditor('option_content_' + '{{ chr($i) }}', '{!! old('option')[chr($i)] !!}');
                    $('#option_error_' + '{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                    @endfor
                    $('#option_error').html('{{ $errors->first('option_radio') }}');
                    $('#option_{{ old('option_radio') }}').find('div').eq(0).click();
                });
            </script>
            @elseif(  $context['status']['type'] == 'FillInTheBlank' )      {{-- 填空题 --}}
            <div class="mt-20" id="option">
                {{-- 填空题答案 --}}
                {{--<div id="option_content_A" class="btn-group cl mb-10" data-content="A">
                    <a class="btn btn-secondary radius f-l" style="width: 36px;">A</a>
                    <input type="text" name="option[A]" class="input-text radius f-l" style="width: 300px;">
                    <a style="text-decoration:none" class="btn btn-danger radius f-l" onClick="batchDeletion('del')" href="javascript:;" title="删除">
                        <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                    </a>
                </div>
                <div id="option_content_B" class="btn-group cl mb-10" data-content="B">
                    <a class="btn btn-secondary radius f-l" style="width: 36px;">B</a>
                    <input type="text" name="option[B]" class="input-text radius f-l" style="width: 300px;">
                    <a style="text-decoration:none" class="btn btn-danger radius f-l" onClick="batchDeletion('del')" href="javascript:;" title="删除">
                        <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                    </a>
                </div>--}}
            </div>
            <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
            <script>
                $(function () {
                    getUeditor('description', '{!! old('description') !!}');
                    getUeditor('analysis', '{!! old('analysis') !!}');
                    //追加失去焦点事件
                    UE.getEditor('description').addListener('blur',function () {
                        var ue = UE.getEditor('description');
                        var ueObj = $(ue.getContent());
                        var ueOptionCount = ueObj.find('input[type=button]').length;
                        var realOptionCount = $('#option .btn-group').length
                        if(ueOptionCount < realOptionCount){
                            //遍历编辑器内答案数，同步删除下方按钮数
                            for(var code=65;code<65+realOptionCount;code++){
                                if(!ueObj.find('#option_'+String.fromCharCode(code)).length){
                                    $('#option_content_'+String.fromCharCode(code)).remove();
                                }
                            }

                            //遍历编辑器内按钮顺序
                            var word = 'A';
                            ueObj.find('input[type=button]').each(function () {
                                // console.log($(this).attr('id'))
                                $(this).attr('id','option_'+word);
                                $(this).val('填空'+word);
                                $(this).data('content',word);
                                word = String.fromCharCode(word.charCodeAt(0)+1);
                            });

                            //遍历填空部分顺序
                            word = 'A';
                            $('#option .btn-group').each(function () {
                                $(this).attr('id','option_content_'+word);
                                $(this).data('content',word);
                                $(this).find('.btn-secondary').html(word);
                                word = String.fromCharCode(word.charCodeAt(0)+1);
                            });

                            //修改编辑器内按钮文字
                            ue.setContent('');
                            var temp = '';
                            ueObj.each(function () {
                                temp += this.outerHTML;
                            });
                            ue.setContent(temp);
                        }
                        // console.log(ueOptionCount,realOptionCount);
                        // console.log($(ue.getContent())[0].outerHTML);
                    });
                    var length = 0;
                    // initOption(65 + length);
                    @for($i=65;$i<67;$i++)
                    $('#option_error_' + '{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                    @endfor
                    $('#option_error').html('{{ $errors->first('option_radio') }}');
                    $('#option_{{ old('option_radio') }}').find('div').eq(0).click();
                });
            </script>
            @elseif(  $context['status']['type'] == 'ShortAnswer' )         {{-- 简答题 --}}
            <div class="mt-20" id="option">
                {{-- 简答题选项 --}}
            </div>
            <input id="addOption" type="button" onclick="addOptions('#option')" class="btn btn-success-outline radius btn-block mt-10" value="添加一个选项">
            <script>
                $(function () {
                    getUeditor('description', '{!! old('description') !!}');
                    getUeditor('analysis', '{!! old('analysis') !!}');
                    @if(Session::has('_old_input'))
                    var length = parseInt('{{ count(old('option')) }}');
                    @else
                    var length = 1;
                    @endif
                    initOption(65 + length);
                    @for($i=65;$i<( null !== old('option') ? count(old('option')) : 1) +65;$i++)
                    getUeditor('option_content_' + '{{ chr($i) }}', '{!! old('option')[chr($i)] !!}');
                    $('#option_error_' + '{{ chr($i) }}').html('{{ $errors->first('option.'.chr($i)) }}');
                    @endfor
                    $('#option_error').html('{{ $errors->first('option_checkbox') }}');
                    @if( null != old('option_checkbox'))
                    @foreach( old('option_checkbox') as $item => $value)
                    $('#option_{{ $item }}').find('div').eq(0).click();
                    @endforeach
                    @endif
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
            @else                                                           {{-- 抛出异常 --}}
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
