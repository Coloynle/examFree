@extends('layouts.iframe')
@section('content')
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
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">下拉框：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <span class="">
                            <select class="select bg-clean" style="padding: 7px;" size="1" name="demo1">
                                <option value="" selected="">默认select</option>
                                <option value="1">菜单一</option>
                                <option value="2">菜单二</option>
                                <option value="3">菜单三</option>
                            </select>
                        </span>
                    </div>
                </div>
                <div>
                </div>
            @elseif(  $context['status']['type'] == 'MultipleChoice' )
            @elseif(  $context['status']['type'] == 'TrueOrFalse' )
            @elseif(  $context['status']['type'] == 'FillInTheBlank' )
            @elseif(  $context['status']['type'] == 'ShortAnswer' )
            @else
                <p> 404 The Page Is Not Found </p>
            @endif
        </form>
    </div>
@endsection
