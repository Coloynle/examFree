@extends('layouts.exam')
<title>{{ config('app.name', 'ExamFree').' - '.$exam['name'] }}</title>
@section('content')
    {{-- ueditor插件 --}}
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
    <nav class="uk-navbar uk-navbar-attached" style="z-index: 1000;" data-uk-sticky>
        <div class="uk-navbar-content">
            <span>用户名：</span>
            <span>{{ Auth::user()->name }}</span>
        </div>
        <div class="uk-navbar-content uk-navbar-flip">
            <a class="uk-button uk-button-primary" onclick="confirmExam();">我要交卷</a>
        </div>
        <div class="uk-navbar-content uk-navbar-flip">
            <span>倒计时：</span>
            <span id="Countdown">
                <span id="day_show"></span>
                <span id="hour_show"></span>
                <span id="minute_show"></span>
                <span id="second_show"></span>
            </span>
        </div>
        <div class="uk-navbar-content uk-navbar-center">
            <span>
                {{ $exam['paper_id']['name'] }}
            </span>
        </div>
    </nav>
    <input type="hidden" value="{{ $exam['id'] }}" name="exam_id">
    <input type="hidden" value="{{ $exam['paper_id']['id'] }}" name="paper_id">
    <div class="uk-grid uk-margin-top">
        <div class="uk-width-1-3 uk-margin-top">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-width-1-2">
                    <div>
                        <!-- 包含拨动元素的垂直选项卡式导航 -->
                        <ul class="uk-nav uk-nav-side" data-uk-tab="{connect:'#paperContent,#paperMainQuestion', animation: 'fade'}">
                            @foreach($exam['paper_id']['content'] as $item => $value)
                                <li>
                                    <a class="uk-text-truncate uk-text-large" style="line-height: 40px;border-radius: 5px;" title="{{ $item }}">{{ $item }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <!-- 包含内容项目的容器 -->
                        <ul id="paperContent" class="uk-switcher">
                            <?php $mainNum = 1 ?>
                            @foreach($exam['paper_id']['content'] as $item => $value)
                                <li>
                                    <ul class="uk-list uk-grid uk-grid-width-1-5" data-uk-margin data-uk-tab="{connect:'#paperMainQuestion{{ $mainNum++ }}', animation: 'fade'}">
                                        @for($i=1;$i<=count($value);$i++)
                                            <li>
                                                <a class="uk-button uk-button-large" id="question-{{ $mainNum-1 }}-{{ $i }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-width-2-3">
            <div class="uk-container uk-container-center">
                <div class="uk-margin-top">
                    <!-- 所有试题 -->
                    <ul id="paperMainQuestion" class="uk-switcher uk-position-relative">
                        <?php $mainNum = 1 ?>
                        @foreach($exam['paper_id']['content'] as $item => $value)
                            <li>
                                <ul id="paperMainQuestion{{ $mainNum++ }}" class="uk-switcher uk-position-relative">
                                    <?php $noNum = 1 ?>
                                    @foreach($value as $questionId => $content)
                                        <li data-uk-margin>
                                            <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                                                <div class="uk-panel-badge uk-badge uk-badge-warning" data-tag="false" data-id="question-{{ $mainNum-1 }}-{{ $noNum }}" onclick="changeTag(this)">标记</div>
                                                <h3 class="uk-panel-title uk-clearfix uk-width-9-10 description" data-questionid="{{ $questionId }}">
                                                    <span class="uk-badge uk-badge-notification uk-float-left uk-margin-small-right">{{ $noNum++ }}</span>
                                                    <span class="uk-badge uk-badge-notification uk-float-left uk-margin-small-right">{{ $content['type'] }}</span>
                                                    @if($content['type'] == '填空题')
                                                        <div class="FillInTheBlank" data-complete="false" data-id="question-{{ $mainNum-1 }}-{{ $noNum-1 }}">
                                                            {!! $content['description'] !!}
                                                        </div>
                                                    @else
                                                        {!! $content['description'] !!}
                                                    @endif
                                                </h3>
                                                <div data-complete="false" data-id="question-{{ $mainNum-1 }}-{{ $noNum-1 }}" data-questionid="{{ $questionId }}">
                                                    @if($content['type'] == '单选题' || $content['type'] == '判断题')
                                                        <form class="uk-form">
                                                            @foreach($content['answer_info'] as $option => $detail)
                                                                <div class="uk-alert">
                                                                    <label class="uk-grid">
                                                                        <div class="uk-width-1-10">
                                                                            <input type="radio" name="{{ $questionId }}" value="{{ $option }}">
                                                                            <span>{{ $option }}.</span>
                                                                        </div>
                                                                        <div class="uk-width-9-10">
                                                                            {!! $detail !!}
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </form>
                                                    @elseif($content['type'] == '多选题')
                                                        <form class="uk-form">
                                                            @foreach($content['answer_info'] as $option => $detail)
                                                                <div class="uk-alert">
                                                                    <label class="uk-grid">
                                                                        <div class="uk-width-1-10">
                                                                            <input type="checkbox" name="{{ $questionId }}[{{ $option }}]" value="{{ $option }}" data-questionid="{{ $questionId }}">
                                                                            <span>{{ $option }}.</span>
                                                                        </div>
                                                                        <div class="uk-width-9-10">
                                                                            {!! $detail !!}
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </form>
                                                    @elseif($content['type'] == '简答题')
                                                        <form class="uk-form">
                                                            @foreach($content['answer_info'] as $option => $detail)
                                                                <div class="uk-alert">
                                                                    <div class="uk-grid">
                                                                        <div class="uk-width-1-10">
                                                                            <span>{{ $option }}.</span>
                                                                        </div>
                                                                        <div class="uk-width-9-10">
                                                                            <script id="{{$questionId}}[{{ $option }}]" name="{{$questionId}}[{{ $option }}]" type="text/plain"></script>
                                                                        </div>
                                                                    </div>
                                                                    <script>
                                                                        $(function () {
                                                                            getUeditor('{{$questionId}}[{{ $option }}]', '', '{{ $questionId }}');
                                                                        });
                                                                    </script>
                                                                </div>
                                                            @endforeach
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="uk-position-absolute">
                                                @if($noNum-2 > 0)
                                                    <a href="javascript:;" onclick="switcher('#question-{{ $mainNum-1 }}-{{ $noNum-2 }}');" class="uk-button">上一题</a>
                                                @endif
                                                @if($noNum <= count($value))
                                                    <a href="javascript:;" onclick="switcher('#question-{{ $mainNum-1 }}-{{ $noNum }}');" class="uk-button">下一题</a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="uk-position-absolute uk-margin-small-top" style="right: 0;">
                                    @if($mainNum-2 > 0)
                                        <a class="uk-button" data-uk-switcher-item="previous">上一道大题</a>
                                    @endif
                                    @if($mainNum <= count($exam['paper_id']['content']))
                                        <a class="uk-button" data-uk-switcher-item="next">下一道大题</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var start_time_type = '{{ $exam['start_time_type'] }}';
        var duration = '{{ $exam['duration'] }}';
        var saveExamUrl = '{{ url('exam/saveExam') }}';
        $(function () {
            //AJAX TOKEN初始化
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (start_time_type == 0) {
                var intDiff = duration * 60;
            } else {
                var nowDate = new Date();
                var startDate = new Date('{{ $exam['exam_time_start'] }}');
                var intDiff = parseInt(duration * 60 - (nowDate - startDate) / 1000);//倒计时总秒数量
            }
            if (intDiff > 0) {
                timer(intDiff);
            } else {
                $('#day_show').html("0天");
                $('#hour_show').html('<s id="h"></s>' + '0时');
                $('#minute_show').html('<s></s>' + '0分');
                $('#second_show').html('<s></s>' + '0秒');
            }

            //防止手误刷新
            // window.onbeforeunload = function () {
            //     return '';
            // };

            $('.description input').each(function () {
                $(this).attr('type', 'text');
                // $(this).val($('#option_answer_info_'+$(this).data('content')).val());
                $(this).attr('class', 'input-text');
                $(this).attr('value', '');
                $(this).attr('name', $(this).parents('.description').data('questionid') + '[' + $(this).data('content') + ']');
                $(this).data('questionid', $(this).parents('.description').data('questionid'));
                // $(this).attr('disabled','true');
                $(this).attr('style', 'width:200px;margin:0 5px')
            });

            /**
             * 判断试题是否作答
             */
            //单选题 & 判断题
            $('input[type=radio]').click(function () {
                //获取存放试题是否作答，是否标记，试题对应答题卡id的div节点
                var divDom = $(this).parents('form').parent();
                divDom.data('complete', true);
                changeComplete(divDom);
            });

            //多选题
            $('input[type=checkbox]').click(function () {
                //获取当前多选questionId
                var questionid = $(this).data('questionid');
                //获取当前多选被选中数量
                var checkLength = $('input[name^=' + questionid + ']:checked').length;
                //获取存放试题是否作答，是否标记，试题对应答题卡id的div节点
                var divDom = $(this).parents('form').parent();
                if (checkLength > 0) {
                    divDom.data('complete', true);
                } else {
                    divDom.data('complete', false);
                }
                changeComplete(divDom);
            });

            //填空题
            $('input[type=text]').blur(function () {
                //获取当前填空questionId
                var questionid = $(this).data('questionid');
                //获取当前填空是否非空
                var textNotNull = true;
                $('input[name^=' + questionid + ']').each(function () {
                    if ($(this).val() == '') {
                        textNotNull = false;
                    }
                });
                //获取存放试题是否作答，是否标记，试题对应答题卡id的div节点
                var divDom = $(this).parents('.FillInTheBlank');
                if (textNotNull) {
                    divDom.data('complete', true);
                } else {
                    divDom.data('complete', false);
                }
                changeComplete(divDom);
            });
            //判断是否作答END
        });

        function timer(intDiff) {
            window.setInterval(function () {
                var day = 0,
                    hour = 0,
                    minute = 0,
                    second = 0;//时间默认值
                if (intDiff > 0) {
                    day = Math.floor(intDiff / (60 * 60 * 24));
                    hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                    minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                    second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
                }
                if (minute <= 9) minute = '0' + minute;
                if (second <= 9) second = '0' + second;
                $('#day_show').html(day + "天");
                $('#hour_show').html('<s id="h"></s>' + hour + '时');
                $('#minute_show').html('<s></s>' + minute + '分');
                $('#second_show').html('<s></s>' + second + '秒');
                intDiff--;
            }, 1000);
        }

        //显示当前id试题
        function switcher(id) {
            $(id)[0].click();
        }

        /* Ueditor创建 */
        function getUeditor(id, content, questionId) {
            content = content || '';
            questionId = questionId || '';
            var ue = UE.getEditor(id, {
                initialFrameWidth: '100%'
            });
            //防止提交表单时，不提交没有使用过的编辑器
            ue.addListener("ready", function () {
                // editor准备好之后才可以使用
                ue.setContent(content);
            });
            //简答题的是否作答方法
            ue.addListener('blur', function () {
                var textareaNotNull = true;
                $('textarea[name^=' + questionId + ']').each(function () {
                    if (!UE.getEditor($(this).attr('name')).hasContents()) {
                        textareaNotNull = false;
                    }
                });
                //获取存放试题是否作答，是否标记，试题对应答题卡id的div节点
                var divDom = $('textarea[name^=' + questionId + ']').parents('form').parent();
                if (textareaNotNull) {
                    divDom.data('complete', true);
                } else {
                    divDom.data('complete', false);
                }
                changeComplete(divDom);
            });
        }

        //改变答题卡是否作答颜色
        function changeComplete(divDom) {
            if (divDom.data('complete')) {
                $('#' + divDom.data('id')).addClass('uk-button-success');
            } else {
                $('#' + divDom.data('id')).removeClass('uk-button-success');
            }
        }

        //改变答题卡标记颜色
        function changeTag(divDom) {
            divDom = $(divDom);
            if (divDom.data('tag')) {
                divDom.data('tag', false);
                $('#' + divDom.data('id')).removeClass('uk-text-warning');
            } else {
                divDom.data('tag', true);
                $('#' + divDom.data('id')).addClass('uk-text-warning');
            }
        }

        function confirmExam(){
            UIkit.modal.confirm("确认提交试卷？", function(){
                submitExam();
            });
        }

        //交卷
        function submitExam() {
            var replyJson = {};
            replyJson['exam_id'] = $('input[name=exam_id]').val();
            replyJson['paper_id'] = $('input[name=paper_id]').val();
            $('input:checked').each(function () {
                replyJson[$(this).attr('name')] = $(this).val();
            });
            $('input[type=text]').each(function () {
                if ($(this).val() != '') {
                    replyJson[$(this).attr('name')] = $(this).val();
                }
            });
            $('textarea').each(function () {
                if ($(this).val() != '') {
                    replyJson[$(this).attr('name')] = $(this).val();
                }
            });

            $.ajax({
                'url': saveExamUrl,
                'async': false,
                'data': replyJson,
                'type': 'POST',
                'success' : function (data) {
                    if(data.code == 1){
                        var modal = UIkit.modal.blockUI('<h2 class="uk-text-center">本次考试得分为'+data.message+'</h2>'+'<p class="uk-text-center">5秒后自动跳转</p>');
                        setTimeout(
                            function(){
                                modal.hide();
                                $(window).attr('location',data.url);
                            }, 5000);
                    }else if(data.code == 2){
                        var modal = UIkit.modal.blockUI('<h2 class="uk-text-center">本次考试客观题目得分为'+data.message+'</h2>'+'<h5 class="uk-text-center">主观题请等待人工判分</h5><p class="uk-text-center">5秒后自动跳转</p>');
                        setTimeout(
                            function(){
                                modal.hide();
                                $(window).attr('location',data.url);
                            }, 5000);
                    }else if(data.code == -1){
                        UIkit.modal.alert("<h2>"+ data.message +"</h2>");
                    }
                }
            });

            console.log(replyJson);
        }
    </script>
@endsection

{{--</body>--}}