@extends('layouts.iframe')
@section('content')
    {{-- 日期插件 --}}
    <script type="text/javascript" src="{{ asset('h-ui/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    {{-- 表格插件 --}}
    {{--<script type="text/javascript" src="lib/datatables/1.10.0/jquery.dataTables.min.js"></script>--}}
    {{-- laypage插件 --}}
    {{--<script type="text/javascript" src="lib/laypage/1.2/laypage.js"></script>--}}
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>

    <div class="page-container">
        <div class="mt-10">
            <form method="POST" action="{{ url('admin/question/manageQuestion/') }}" id="searchFrom">
                <div class="cl">
                    {{ csrf_field() }}
                    <input type="text" name="id" value="{{ $params['id'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 100px" placeholder="试题ID">
                    <input type="text" name="description" value="{{ $params['description'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="试题详情">
                    <input type="hidden" name="type" value="{{ $params['type'] }}">
                    <div class="btn-group f-l mr-10 mt-10" id="questionType">
                        <a class="btn btn-primary radius" href="javascript:;" data-value="" onclick="chooseType(this)">全部</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="SingleChoice" onclick="chooseType(this)">单选题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="MultipleChoice" onclick="chooseType(this)">多选题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="TrueOrFalse" onclick="chooseType(this)">判断题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="FillInTheBlank" onclick="chooseType(this)">填空题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="ShortAnswer" onclick="chooseType(this)">简答题</a>
                    </div>
                </div>
                <div class="cl">
                    <input type="text" name="create_user_name" value="{{ $params['create_user_name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="创建人姓名">
                    <input type="text" name="created_time_start" value="{{ $params['created_time_start'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="创建起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'create_max\')||\'%y-%M-%d\'}' })" id="create_min">
                    <input type="text" name="created_time_end" value="{{ $params['created_time_end'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="创建结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'create_min\')}',maxDate:'%y-%M-%d' })" id="create_max">
                    <input type="text" name="update_user_name" value="{{ $params['update_user_name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="修改人姓名">
                    <input type="text" name="updated_time_start" value="{{ $params['updated_time_start'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="修改起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'update_max\')||\'%y-%M-%d\'}' })" id="update_min">
                    <input type="text" name="updated_time_end" value="{{ $params['updated_time_end'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="修改结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'update_min\')}',maxDate:'%y-%M-%d' })" id="update_max">
                    <input type="submit" class="btn btn-primary radius f-l mr-10 mt-10" value="搜索">
                    <input type="button" class="btn btn-primary radius f-l mr-10 mt-10" onclick="resetFrom();" value="重置">
                </div>
            </form>
        </div>
        <div class="mt-20 cl">
            <div id="questionPage">
                <table class="table table-border table-bordered table-bg table-hover table-sort table-responsive">
                    <thead>
                    <tr class="text-c">
                        <th width="25"><input type="checkbox" name="" value=""></th>
                        <th width="80">ID</th>
                        <th>详情</th>
                        <th width="80">题型</th>
                        <th width="80">创建人</th>
                        <th width="120">创建时间</th>
                        <th width="80">修改人</th>
                        <th width="120">修改时间</th>
                        <th width="75">浏览次数</th>
                        <th width="60">发布状态</th>
                        <th width="120">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--{{ dd($questions) }}--}}

                    @foreach( $questions as $item => $value )
                        {{--{{ dd($value) }}--}}
                        <tr class="text-c">
                            <td><input type="checkbox" value="{{ $value['id'] }}" name="questionId"></td>
                            <td>{{ $value['id'] }}</td>
                            <td class="text-l text-overflow">
                                <u style="cursor:pointer" class="text-primary" onClick="article_edit('查看','article-zhang.html','10001')" title="查看">
                                    {{-- 使用 strip_tags 去除字符串中的HTML标识 --}}
                                    {{ strip_tags($value['description']) }}
                                </u>
                            </td>
                            <td title="{{ config('exam.question_type.'.$value['type'],'未知') }}">{{ config('exam.question_type.'.$value['type'],'未知') }}</td>
                            <td title="{{ $value['getCreateUserName']['name'] }}">{{ $value['getCreateUserName']['name'] }}</td>
                            <td title="{{ $value['created_at'] }}">{{ $value['created_at'] }}</td>
                            <td title="{{ $value['getUpdateUserName']['name'] or '无'}}">{{ $value['getUpdateUserName']['name'] or '无'}}</td>
                            <td title="{{ $value['updated_at'] }}">{{ $value['updated_at'] }}</td>
                            <td>21212</td>
                            <td class="td-status"><span class="label label-success radius">已发布</span></td>
                            <td class="f-14 td-manage">
                                <a style="text-decoration:none" onClick="article_stop(this,'10001')" href="javascript:;" title="下架">
                                    <i class="Hui-iconfont">&#xe6de;</i>
                                </a>
                                <a style="text-decoration:none" class="ml-5" onClick="article_edit('资讯编辑','article-add.html','10001')" href="javascript:;" title="编辑">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                <a style="text-decoration:none" class="ml-5" onClick="article_del(this,'10001')" href="javascript:;" title="删除">
                                    <i class="Hui-iconfont">&#xe6e2;</i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-20 f-l" id="DataTables_Table_0_info" role="status" aria-live="polite"> {{ $questions->currentPage() }} / {{ $questions->lastPage() }} ，共 {{ $questions->total() }} 条</div>
            <div class="f-r">
                {{--                {{ $questions->render() }}--}}
                {!! $questions->links() !!}
            </div>
        </div>
    </div>
    <script>
        {{-- 选择试题类型事件 --}}
        function chooseType(that) {
            $(that).siblings('a').removeClass('btn-primary');
            $(that).siblings('a').removeClass('btn-default');
            $(that).siblings('a').addClass('btn-default');
            $(that).removeClass('btn-default');
            $(that).removeClass('btn-primary');
            $(that).addClass('btn-primary');
            $('input[name=type]').val($(that).data('value'));
        }

        //重置搜索表单,并且提交表单
        function resetFrom() {
            $('#searchFrom input[type=text]').each(function () {
                    $(this).val('');
            })
            $('input[name=type]').val('');
            $('#searchFrom').submit();
        }

        //页面加载完毕后运行,保证试题类型选择样式正确
        $(function () {
            var type = $('input[name=type]').val() || '';
            $('#questionType a').each(function () {
                if($(this).data('value') == type){
                    $(this).removeClass('btn-default');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-primary');
                }else{
                    $(this).removeClass('btn-default');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-default');
                }
            })
        });
        {{--function searchCondition() {--}}
            {{--var params = {--}}
                {{--'_token': '{{ csrf_token() }}',--}}
                {{--'id': $('input[name=id]').val(),--}}
                {{--'description': $('input[name=description]').val(),--}}
                {{--'type': $('input[name=type]').val(),--}}
                {{--'create_user_name': $('input[name=create_user_name]').val(),--}}
                {{--'update_user_name': $('input[name=update_user_name]').val(),--}}
                {{--'created_time_start': $('input[name=created_time_start]').val(),--}}
                {{--'created_time_end': $('input[name=created_time_end]').val(),--}}
                {{--'updated_time_start': $('input[name=updated_time_start]').val(),--}}
                {{--'updated_time_end': $('input[name=updated_time_end]').val()--}}
            {{--};--}}
            {{--$.ajax({--}}
                {{--'url': '{{ url('admin/question/manageQuestion/') }}',--}}
                {{--'data': params,--}}
                {{--'type': 'POST',--}}
                {{--'dataType': 'json',--}}
                {{--'success': function (data) {--}}
                    {{--console.log(data);--}}
                {{--}--}}
            {{--})--}}
        {{--}--}}
    </script>
@endsection