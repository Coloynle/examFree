@extends('layouts.iframe')
@section('content')
    {{-- 日期插件 --}}
    <script type="text/javascript" src="{{ asset('h-ui/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    {{-- 公用管理Js --}}
    <script type="text/javascript" src="{{ asset('js/admin/featuresForManage.js') }}"></script>
    {{-- ueditor插件 --}}
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

        var showPaperUrl = '{{ url('admin/paper/previewPaper').'/'}}';
        var getPaperId = '{{ url('admin/paper/getPaperById') }}';
        var addPaperId = '{{ url('admin/paper/managePaper/true') }}';
        var tbodyDom = '';
    </script>
    <script type="text/javascript" src="{{ asset('js/admin/exam/addExam.js') }}"></script>

    <div class="page-container">
        <form method="POST" action="{{ url('admin/exam/createExam') }}">
            {{ csrf_field() }}
            <input name="examId" type="hidden" class="input-text radius"  value="{{ old('examId') }}">
            <input name="examName" type="text" class="input-text radius" placeholder="考试名称" value="{{ old('examName') }}">
            <span class="c-error">{{ $errors->first('examName') }}</span>
            <input type="hidden" name="type" value="{{ old('type') == '' ? 0 : old('type')}}">
            <div class="cl">
                <div class="btn-group f-l mr-10 mt-10" id="examType">
                    <a class="btn btn-default radius" href="javascript:;" data-value="0" onclick="chooseButton(this,$('input[name=type]'));$('#apply_min').hide();$('#apply_max').hide();">无需报名</a>
                    <a class="btn btn-default radius" href="javascript:;" data-value="1" onclick="chooseButton(this,$('input[name=type]'));$('#apply_min').show();$('#apply_max').show();">需要报名</a>
                </div>
                <span class="c-error">{{ $errors->first('type') }}</span>
                <input type="text" name="exam_time_start" value="{{ old('exam_time_start') }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="考试起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'exam_max\')}' })" id="exam_min">
                <span class="c-error f-l mt-15 mr-10">{{ $errors->first('exam_time_start') }}</span>
                <input type="text" name="exam_time_end" value="{{ old('exam_time_end') }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="考试结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'exam_min\')}' })" id="exam_max">
                <span class="c-error f-l mt-15 mr-10">{{ $errors->first('exam_time_end') }}</span>
                <input type="text"
                       @if(old('type') == 0 || old('type') == '')
                       hidden
                       @endif
                       name="apply_time_start" value="{{ old('apply_time_start') }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="报名起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'apply_max\')}' })" id="apply_min">
                <span class="c-error f-l mt-15 mr-10">{{ $errors->first('apply_time_start') }}</span>
                <input type="text"
                       @if(old('type') == 0 || old('type') == '')
                       hidden
                       @endif
                       name="apply_time_end" value="{{ old('apply_time_end') }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="报名结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'apply_min\')}' })" id="apply_max">
                <span class="c-error f-l mt-15 mr-10">{{ $errors->first('apply_time_end') }}</span>
                <input type="text" name="sort" value="{{ old('sort') }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="考试分类">
                <span class="c-error f-l mt-15 mr-10">{{ $errors->first('sort') }}</span>
                <input type="hidden" name="start_time_type" value="{{ old('start_time_type') == '' ? 0 : old('start_time_type')}}">
                <div class="btn-group f-l mr-10 mt-10" id="start_time_type">
                    <a class="btn btn-default radius" href="javascript:;" data-value="0" onclick="chooseButton(this,$('input[name=start_time_type]'));">试卷打开时间</a>
                    <a class="btn btn-default radius" href="javascript:;" data-value="1" onclick="chooseButton(this,$('input[name=start_time_type]'));">考试开始时间</a>
                </div>
                <input name="duration" type="number" min="0" class="input-text radius f-l mr-10 mt-10" style="width: 130px;" placeholder="考试时长 / 分钟">
            </div>
            <div class="panel panel-secondary-change radius mt-20">
                <div class="panel-header">
                    <span>考试描述</span>
                    <span class="ml-40 c-error">{{ $errors->first('description') }}</span>
                </div>
                <div class="panel-body">
                    <script id="description" name="description" type="text/plain"></script>
                </div>
            </div>
            <div class="mt-20 cl">
                <input type="hidden" name="paper_id" id="paper_id" value="{{ old('paper_id') }}">
                <table class="radius table table-border table-bordered table-bg table-hover table-sort table-responsive dataTable" style="table-layout:fixed;">
                    <thead>
                    <tr class="text-c">
                        <th width="25"><input type="checkbox" name="" value=""></th>
                        <th width="80">ID</th>
                        <th width="400">试卷名称</th>
                        <th width="80">试卷分类</th>
                        <th width="80">试卷总分</th>
                        <th width="80">及格分数</th>
                        <th width="80">操作</th>
                    </tr>
                    </thead>
                    <tbody data-count="0">
                    @if(old('paper_id') != '')
                        <script>
                            $(function () {
                                tbodyDom = $('#choosePaper').parent().find('tbody');
                                paperAdd('{{ old('paper_id') }}');
                            });
                        </script>
                    @else
                        <tr class="text-c">
                            <td colspan="7" id="emptyDom">没有数据</td>
                        </tr>
                    @endif

                    </tbody>
                </table>
                <input id="choosePaper" type="button" onclick="paperLayer(this)" class="btn btn-success-outline radius f-l" style="margin: 22px 0" value="选择试卷">
                <span class="c-error f-l mt-25 ml-10">{{ $errors->first('paper_id') }}</span>
                <ul class="pagination f-r">
                    <li>
                        <a style="text-decoration:none" onClick="papersRemove()" href="javascript:;" title="批量删除">
                            <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                        </a>
                    </li>
                </ul>
            </div>
            <input id="saveExam" type="submit" class="btn btn-success-outline radius btn-block mt-10" value="保存考试">
        </form>
    </div>

    <script>
        getUeditor('description', '{!! old('description') !!}');
    </script>
@endsection