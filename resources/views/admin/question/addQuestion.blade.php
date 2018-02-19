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
            var code = $(divObj).find('.panel-secondary').last().data('content');
            if(code.charCodeAt(0) >= 90 || code.charCodeAt(0) < 65){
                $.Huimodalalert('选项数量到达上限',2000);
                return false;
            }else{
                var word = String.fromCharCode(code.charCodeAt(0) + 1);
            }
            var divHtml = "<div class=\"panel panel-secondary\" data-content=\""+ word +"\" id=\"option_"+ word +"\">" +
                                "<a class=\"btn btn-danger radius f-r\" style=\"margin-top: 3px;margin-right: 5px;\" href=\"javascript:;\" onclick=\"deleteOption(this)\">" +
                                    "<i class=\"Hui-iconfont Hui-iconfont-del3\"></i>" +
                                "</a>" +
                                "<div class=\"panel-header\" onclick=\"checkRadio(this);\">" +
                                    "<div class=\"radio-box\">\n" +
                                        "<input type=\"radio\" id=\"option_radio_"+ word +"\" name=\"option_radio\">\n" +
                                        "<label for=\"option_radio_"+ word +"\">"+ word +"</label>" +
                                    "</div>" +
                                "</div>" +
                                "<div class=\"panel-body\">" +
                                    "<script id=\"option_content_"+ word +"\" class=\"ueditor\" name=\"option_"+ word +"\" type=\"text/plain\"><\/script>"+
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
            var endCode = $('#option').find('.panel-secondary').last().data('content');
            $('#model_true').attr('onclick','resetCode("'+option+'","'+startCode+'","'+endCode+'")');
            $("#modal").modal("show");
        }

        /* 内容转移删除最后一个Ueditor */
        function resetCode(option,startCode,endCode) {
            var asciiCode = startCode.charCodeAt(0);
            var flag = false;
            if(startCode != endCode) {
                $('.ueditor').each(function () {
                    console.log(this);
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
                    console.log(asciiCode);
                    // that.children().find('.radio-box input').attr('id','option_radio_'+String.fromCharCode(asciiCode));
                    // that.children().find('.radio-box label').attr('for','option_radio_'+String.fromCharCode(asciiCode));
                    // that.children().find('.radio-box label').html(String.fromCharCode(asciiCode));
                    // that.children().find('.ueditor').attr('id','option_content_'+String.fromCharCode(asciiCode));
                    // that.children().find('.ueditor').attr('name','option_'+String.fromCharCode(asciiCode));
                    // asciiCode++;
                });
            }
            $('#option_'+endCode).remove();
            UE.getEditor('option_content_'+endCode).destroy();
            $('[name=option_'+endCode+']').remove();
            $('#modal').modal('hide');
        }

        function singleChoiceCount(){
            var count = $('#option').find('.panel-secondary').length;
            if(count == 3){
                $.Huimodalalert('选项数量不可小于3个',2000);
                return false;
            }else{
                return true;
            }
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
                        <a class="btn btn-danger radius f-r" style="margin-top: 3px;margin-right: 5px;" href="javascript:;" onclick="deleteOption(this)">
                            <i class="Hui-iconfont Hui-iconfont-del3"></i>
                        </a>
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
                    <div class="panel panel-secondary" data-content="B" id="option_B">
                        <a class="btn btn-danger radius f-r" style="margin-top: 3px;margin-right: 5px;" href="javascript:;" onclick="deleteOption(this)">
                            <i class="Hui-iconfont Hui-iconfont-del3"></i>
                        </a>
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_B" name="option_radio">
                                <label for="option_radio_B">B</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_B" name="option_B" class="ueditor" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="panel panel-secondary" data-content="C" id="option_C">
                        <a class="btn btn-danger radius f-r" style="margin-top: 3px;margin-right: 5px;" href="javascript:;" onclick="deleteOption(this)">
                            <i class="Hui-iconfont Hui-iconfont-del3"></i>
                        </a>

                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_C" name="option_radio">
                                <label for="option_radio_C">C</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_C" name="option_C" class="ueditor" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="panel panel-secondary" data-content="D" id="option_D">
                        <a class="btn btn-danger radius f-r" style="margin-top: 3px;margin-right: 5px;" href="javascript:;" onclick="deleteOption(this)">
                            <i class="Hui-iconfont Hui-iconfont-del3"></i>
                        </a>
                        <div class="panel-header" onclick="checkRadio(this);">
                            <div class="radio-box">
                                <input type="radio" id="option_radio_D" name="option_radio">
                                <label for="option_radio_D">D</label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <script id="option_content_D" name="option_D" class="ueditor" type="text/plain"></script>
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
        </form>
    </div>
@endsection
