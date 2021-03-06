@extends('layouts.app')
<title>{{ config('app.name','ExamFree').' - Exam' }}</title>
@section('content')
    <div class="uk-container uk-container-center">
        <!-- 位于左边的选项卡式导航 -->
        <div>
            <div>
                <!-- 包含拨动元素的垂直选项卡式导航 -->
                <ul class="uk-tab" data-uk-tab="{connect:'#exam', animation: 'fade'}">
                    <li class="uk-active">
                        <a class="uk-text-center">全部考试</a>
                    </li>
                </ul>
            </div>
            <div class="uk-margin-top">
                <!-- 包含内容项目的容器 -->
                <ul id="exam" class="uk-switcher">
                    <li>
                        <div class="uk-grid">
                            @foreach($exams as $item => $exam)
                                <div class="uk-width-1-3 uk-margin-bottom">
                                    <figure class="uk-overlay uk-overlay-hover width-height-6-4">
                                        <img src="{{ $exam['img'] == '' ? asset('uikit/placeholder-img.svg') : asset('storage/'.$exam['img']) }}" onerror="this.src='{{ asset('uikit/placeholder-img.svg') }}'">
                                        <figcaption class="uk-overlay-panel uk-overlay-background uk-overlay-slide-left">
                                            <p class="uk-text-center">考试开始时间：{{ $exam['exam_time_start'] }}</p>
                                            <p class="uk-text-center">考试结束时间：{{ $exam['exam_time_end'] }}</p>
                                            @if($exam['type'] == 1)
                                                <p class="uk-text-center">报名开始时间：{{ $exam['apply_time_start'] }}</p>
                                                <p class="uk-text-center">报名结束时间：{{ $exam['apply_time_end'] }}</p>
                                            @endif
                                            <a class="uk-position-cover" href="{{ url('exam/showExam/'.$exam['id']) }}"></a>
                                        </figcaption>
                                    </figure>
                                    <p class="uk-text-center uk-text-truncate" title="{{ $exam['name'] }}">
                                        {{ $exam['name'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="uk-clearfix">
                            <div class="uk-float-left ml-20" id="DataTables_Table_0_info" role="status" aria-live="polite">
                            <span class="pagination" style="line-height: 40px">
                                <span>
                                    {{ $exams->currentPage() }} / {{ $exams->lastPage() }}
                                </span>，
                                <span id="total" data-total="{{ $exams->total() }}">
                                    共 {{ $exams->total() }} 条
                                </span>
                            </span>
                            </div>
                            <div class="uk-float-right">
                                {!! $exams->links() !!}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
