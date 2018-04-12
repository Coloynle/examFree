@extends('layouts.app')
<title>{{ config('app.name','ExamFree').' - Home' }}</title>
@section('content')
    <div class="uk-container uk-container-center">
        <!-- 位于左边的选项卡式导航 -->
        <div class="uk-grid">
            <div class="uk-width-medium-1-6">
                <!-- 包含拨动元素的垂直选项卡式导航 -->
                <ul class="uk-tab uk-tab-left" data-uk-tab="{connect:'#exam', animation: 'fade'}">
                    <li class="uk-active">
                        <a class="uk-text-center">最新考试</a>
                    </li>
                    <li>
                        <a class="uk-text-center">最热考试</a>
                    </li>
                </ul>
            </div>
            <div class="uk-width-medium-5-6">
                <!-- 包含内容项目的容器 -->
                <ul id="exam" class="uk-switcher">
                    <li>
                        <div class="uk-grid uk-flex">
                            @foreach($newestExam as $item => $exam)
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
                    </li>
                    <li>
                        22我的天
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
