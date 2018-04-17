@extends('layouts.app')
<title>{{ config('app.name','ExamFree').' - Exam' }}</title>
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="uk-width-1-2">
                <figure class="uk-thumbnail width-height-6-4 uk-overflow-hidden">
                    <img src="{{ $exam['img'] == '' ? asset('uikit/placeholder-img.svg') : asset('storage/'.$exam['img']) }}" onerror="this.src='{{ asset('uikit/placeholder-img.svg') }}'">
                </figure>
            </div>
            <div class="uk-width-1-2 uk-position-relative">
                <h3 class="uk-text-truncate" title="{{ $exam['name'] }}">{{ $exam['name'] }}</h3>
                <p class="uk-text-truncate">考试分类：<span title="{{ $exam['sort'] }}">{{ $exam['sort'] }}</span></p>
                <p>考试时间：{{ $exam['exam_time_start'] }} ~ {{ $exam['exam_time_end'] }}</p>
                @if($exam['type'] == 1)
                <p>报名时间：{{ $exam['apply_time_start'] }} ~ {{ $exam['apply_time_end'] }}</p>
                @endif
                <p>考试时长计算方式：{{ $exam['start_time_type'] == 1 ? '考试开始时间' : '试卷打开时间' }}</p>
                <p>考试时长：{{ $exam['duration'] }}</p>
                <p>创建人：<span title="{{ $exam['get_create_user_name']['name'] }}">{{ $exam['get_create_user_name']['name'] }}</span></p>
                <p>创建时间：<span>{{ $exam['created_at'] }}</span></p>
                <a class="uk-button uk-button-success uk-button-large uk-text-bottom uk-width-1-4 uk-position-absolute" style="bottom: 0" onclick="startExam('{{ $exam['id'] }}')" href="javascript:;">
                    开始考试
                </a>
            </div>
        </div>
        <div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
            <div class="uk-panel uk-panel-box">
                <h4 class="uk-panel-title">
                    考试描述
                </h4>
                <span>
                    {!! $exam['description'] !!}
                </span>
            </div>
        </div>
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
        var checkPermissionUrl = '{{ url('exam/checkPermission') }}';
        function startExam(id) {
            $.ajax({
                'type' : 'POST',
                'url' : checkPermissionUrl,
                'data' : {
                    'id' : id
                },
                'success' : function (data) {
                    $(window).attr('location',data);
                }
            })
        }
    </script>
@endsection