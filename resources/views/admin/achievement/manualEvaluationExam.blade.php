@extends('layouts.iframe')
@section('content')
    {{-- 日期插件 --}}
    <script type="text/javascript" src="{{ asset('h-ui/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/featuresForManage.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/exam/manageExam.js') }}"></script>
    {{-- 表格插件 --}}
    {{--<script type="text/javascript" src="lib/datatables/1.10.0/jquery.dataTables.min.js"></script>--}}
    {{-- laypage插件 --}}
    {{--<script type="text/javascript" src="lib/laypage/1.2/laypage.js"></script>--}}
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>
    {{--<input type="hidden" id="deleteExam" value="{{ url('admin/exam/deleteExam/') }}">--}}
    <input type="hidden" id="currentPage" value="{{ $examResultPaginate->url($examResultPaginate->currentPage()) }}">
    {{--<input type="hidden" id="statusExam" value="{{ url('admin/exam/statusExam/') }}">--}}
    <input type="hidden" id="params" value='{!! serialize($params) !!}'>
    <div class="page-container">
        <div class="mt-10">
            <form method="POST" action="{{ url(Request::getPathInfo()) }}" id="searchFrom">
                <div class="cl">
                    {{ csrf_field() }}
                    <input type="text" name="id" value="{{ $params['id'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 100px" placeholder="考试ID">
                    <input type="text" name="name" value="{{ $params['name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="考试名称">
                    <input type="text" name="exam_time_start" value="{{ $params['exam_time_start'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="考试起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'exam_max\')}',dateFmt:'yyyy-MM-dd HH:mm:ss' })" id="exam_min">
                    <input type="text" name="exam_time_end" value="{{ $params['exam_time_end'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="考试结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'exam_min\')}',dateFmt:'yyyy-MM-dd HH:mm:ss' })" id="exam_max">
                    <input type="text" name="create_user_name" value="{{ $params['create_user_name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="交卷用户">
                    <input type="text" name="created_time_start" value="{{ $params['created_time_start'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="交卷起始日期" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'create_max\')||\'%y-%M-%d\'}' })" id="create_min">
                    <input type="text" name="created_time_end" value="{{ $params['created_time_end'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 186px" placeholder="交卷结束日期" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'create_min\')}',maxDate:'%y-%M-%d' })" id="create_max">
                    <input type="submit" class="btn btn-primary radius f-l mr-10 mt-10" value="搜索">
                    <input type="button" class="btn btn-primary radius f-l mr-10 mt-10" onclick="resetFrom();" value="重置">
                </div>
            </form>
        </div>
        <div class="mt-20 cl">
            <div id="examPage">
                <table class="table table-border table-bordered table-bg table-hover table-sort table-responsive dataTable" style="table-layout:fixed;">
                    <thead>
                    <tr class="text-c">
                        <th width="25"><input type="checkbox" name="" value=""></th>
                        <th width="25">ID</th>
                        <th width="170">考试名称</th>
                        <th>考试起始时间</th>
                        <th>考试结束时间</th>
                        <th>交卷用户</th>
                        <th class="sorting{{ !empty($params['order_by_created_time']) ? '_'.$params['order_by_created_time'] : '' }}" id="order_by_created_time" data-order="{{ $params['order_by_created_time'] }}" onclick="orderPage(this,'{{ $examResultPaginate->url($examResultPaginate->currentPage())
                        }}')">交卷时间
                        </th>
                        @if(isset($context['status']['score']) && $context['status']['score'])
                        <th>分数</th>
                        @endif
                        <th width="90">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $examResultPaginate as $item => $value )
                        <tr class="text-c">
                            <td><input type="checkbox" value="{{ $value['getExam']['id'] }}" name="examId"></td>
                            <td>{{ $value['getExam']['id'] }}</td>
                            <td class="text-l text-overflow">
                                <u class="text-primary text-overflow" style="width: 170px" title="{{ strip_tags($value['getExam']['name']) }}">
                                    {{-- 使用 strip_tags 去除字符串中的HTML标识 --}}
                                    {{ strip_tags($value['getExam']['name']) }}
                                </u>
                            </td>
                            <td title="{{ $value['getExam']['exam_time_start'] }}">{{ $value['getExam']['exam_time_start'] }}</td>
                            <td title="{{ $value['getExam']['exam_time_end'] }}">{{ $value['getExam']['exam_time_end'] }}</td>
                            <td title="{{ $value['getExamUser']['name'] }}">{{ $value['getExamUser']['name'] }}</td>
                            <td title="{{ $value['created_at'] }}">{{ $value['created_at'] }}</td>
                            @if(isset($context['status']['score']) && $context['status']['score'])
                                <td>{{ $value['getScore']['score'] }}</td>
                            @endif
                            <td class="f-14 td-manage">
                                @if(isset($context['status']['score']) && !$context['status']['score'])
                                <a style="text-decoration:none" class="ml-5" onclick="showLayer('评分','{{ url('admin/achievement/startEvaluation').'/'.$value['id'] }}')" title="编辑" href="javascript:;">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                @endif
                                <a style="text-decoration:none" class="ml-5" onClick="deleteOne('{{ $value['id'] }}')" href="javascript:;" title="删除">
                                    <i class="Hui-iconfont Hui-iconfont-del2"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($examResultPaginate->total()))
                        <tr class="text-c">
                            <td colspan="
                            @if(isset($context['status']['score']) && $context['status']['score'])
                                    9
                            @else
                                    8
                            @endif
                            ">没有数据</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="f-l">
                <ul class="pagination">
                    <li>
                        <a style="text-decoration:none" onClick="batchDeletion('del','examId')" href="javascript:;" title="批量删除">
                            <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="f-l ml-20" id="DataTables_Table_0_info" role="status" aria-live="polite">
                <span class="pagination" style="line-height: 40px">
                    <span>
                        {{ $examResultPaginate->currentPage() }} / {{ $examResultPaginate->lastPage() }}
                    </span>，
                    <span id="total" data-total="{{ $examResultPaginate->total() }}">
                        共 {{ $examResultPaginate->total() }} 条
                    </span>
                </span>
            </div>
            <div class="f-r">
                {!! $examResultPaginate->links() !!}
            </div>
        </div>
    </div>
    <div id="modal-del" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content radius">
                <div class="modal-header">
                    <h3 class="modal-title">批量删除</h3>
                    <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:;">×</a>
                </div>
                <div class="modal-body">
                    <div class="skin-minimal text-c">
                        <div class="radio-box mr-50">
                            <label class="f-l">
                                <input type="radio" name="deleteType" value="0" id="choose-del">
                                <span>删除选中考试</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="deleteType" value="1">
                                <span>删除检索考试</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="deleteAnyExam()">确定</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-release" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content radius">
                <div class="modal-header">
                    <h3 class="modal-title">批量发布</h3>
                    <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:;">×</a>
                </div>
                <div class="modal-body">
                    <div class="skin-minimal text-c">
                        <div class="radio-box mr-50">
                            <label class="f-l">
                                <input type="radio" name="releaseType" value="0" id="choose-release">
                                <span>发布选中考试</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="releaseType" value="1">
                                <span>发布检索考试</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="statusChangeExam('release','releaseType',0)">确定</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-underTheShelf" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content radius">
                <div class="modal-header">
                    <h3 class="modal-title">批量下架</h3>
                    <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:;">×</a>
                </div>
                <div class="modal-body">
                    <div class="skin-minimal text-c">
                        <div class="radio-box mr-50">
                            <label class="f-l">
                                <input type="radio" name="underTheShelfType" value="0" id="choose-underTheShelf">
                                <span>下架选中考试</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="underTheShelfType" value="1">
                                <span>下架检索考试</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="statusChangeExam('underTheShelf','underTheShelfType',1)">确定</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        //页面加载完毕后运行,显示获得的消息
        $(function () {
            @if(Session::has('code'))
            $.Huimodalalert('{{ Session::get('message') }}', 2000);
            @endif
        });
        0
    </script>
@endsection