@extends('layouts.app')
<title>{{ config('app.name','ExamFree').' - Exam' }}</title>
@section('content')
    <div class="uk-container uk-container-center">
        <!-- 位于左边的选项卡式导航 -->
        <div class="uk-grid">
            @foreach($achievements as $item => $achievement)
                <div class="uk-width-1-3 uk-margin-bottom">
                    <figure class="uk-overlay width-height-6-4">
                        <figcaption class="uk-overlay-panel uk-overlay-background">
                            <h1 class="uk-text-center">{{ $achievement['getScore']['score'] }}</h1>
                            <h1 class="uk-text-center">{{ $achievement['getScore']['score'] >= $achievement['getPaper']['passing_score'] ? '及格' : '不及格' }}</h1>
                        </figcaption>
                    </figure>
                    <p class="uk-text-center uk-text-truncate" title="{{ $achievement['getExam']['name'] }}">
                        {{ $achievement['getExam']['name'] }}
                    </p>
                </div>
            @endforeach
        </div>
        <div class="uk-clearfix">
            <div class="uk-float-left ml-20" id="DataTables_Table_0_info" role="status" aria-live="polite">
            <span class="pagination" style="line-height: 40px">
                <span>
                    {{ $achievements->currentPage() }} / {{ $achievements->lastPage() }}
                </span>，
                <span id="total" data-total="{{ $achievements->total() }}">
                    共 {{ $achievements->total() }} 条
                </span>
            </span>
            </div>
            <div class="uk-float-right">
                {!! $achievements->links() !!}
            </div>
        </div>
    </div>
@endsection
