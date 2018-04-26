@extends('layouts.exam')
<title>{{ config('app.name', 'ExamFree').' - '.$exam['name'] }}</title>
@section('content')
    <script type="text/javascript">
        var start_time_type = '{{ $exam['start_time_type'] }}';
        var duration = '{{ $exam['duration'] }}';
        var saveExamUrl = '{{ url('exam/saveExam') }}';
        var exam_time_start = '{{ $exam['exam_time_start'] }}';
    </script>
    {{-- ueditor插件 --}}
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/ueditor.all.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('h-ui/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('js/exam/startExam.js') }}"></script>
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

@endsection

{{--</body>--}}