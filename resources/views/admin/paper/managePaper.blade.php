@extends('layouts.iframe')
@section('content')
    {{-- 日期插件 --}}
    <script type="text/javascript" src="{{ asset('h-ui/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/featuresForManage.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/paper/managePaper.js') }}"></script>
    {{-- 表格插件 --}}
    {{--<script type="text/javascript" src="lib/datatables/1.10.0/jquery.dataTables.min.js"></script>--}}
    {{-- laypage插件 --}}
    {{--<script type="text/javascript" src="lib/laypage/1.2/laypage.js"></script>--}}
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>
    <input type="hidden" id="deletePaper" value="{{ url('admin/paper/deletePaper/') }}">
    <input type="hidden" id="currentPage" value="{{ $papers->url($papers->currentPage()) }}">
    <input type="hidden" id="statusPaper" value="{{ url('admin/paper/statusPaper/') }}">
    <input type="hidden" id="params" value='{!! serialize($params) !!}'>
    <div class="page-container">
        <div class="mt-10">
            <form method="POST" action="{{ url('admin/paper/managePaper/') }}" id="searchFrom">
                <div class="cl">
                    {{ csrf_field() }}
                    <input type="text" name="id" value="{{ $params['id'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 100px" placeholder="试卷ID">
                    <input type="text" name="description" value="{{ $params['name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="试卷名称">
                    {{--<input type="hidden" name="type" value="{{ $params['type'] }}">--}}
                    <input type="hidden" name="status" value="{{ $params['status'] }}">
                  {{--  <div class="btn-group f-l mr-10 mt-10" id="questionType">
                        <a class="btn btn-primary radius" href="javascript:;" data-value="" onclick="chooseType(this)">全部</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="SingleChoice" onclick="chooseType(this)">单选题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="MultipleChoice" onclick="chooseType(this)">多选题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="TrueOrFalse" onclick="chooseType(this)">判断题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="FillInTheBlank" onclick="chooseType(this)">填空题</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="ShortAnswer" onclick="chooseType(this)">简答题</a>
                    </div>--}}
                    <div class="btn-group f-l mr-10 mt-10" id="paperStatus">
                        <a class="btn btn-default radius" href="javascript:;" data-value="" onclick="chooseButton(this,$('input[name=status]'))">全部</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="0" onclick="chooseButton(this,$('input[name=status]'))">已发布</a>
                        <a class="btn btn-default radius" href="javascript:;" data-value="1" onclick="chooseButton(this,$('input[name=status]'))">未发布</a>
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
            <div id="paperPage">
                <table class="table table-border table-bordered table-bg table-hover table-sort table-responsive dataTable" style="table-layout:fixed;">
                    <thead>
                    <tr class="text-c">
                        <th width="25"><input type="checkbox" name="" value=""></th>
                        <th width="80" class="sorting{{ !empty($params['order_by_id']) ? '_'.$params['order_by_id'] : '' }}" id="order_by_id" data-order="{{ $params['order_by_id'] }}" onclick="orderPage(this,'{{ $papers->url($papers->currentPage()) }}')">ID</th>
                        <th width="400">试卷名称</th>
                        <th width="80" class="sorting{{ !empty($params['order_by_type']) ? '_'.$params['order_by_type'] : '' }}" id="order_by_type" data-order="{{ $params['order_by_type'] }}" onclick="orderPage(this,'{{ $papers->url($papers->currentPage()) }}')">分类</th>
                        <th class="sorting{{ !empty($params['order_by_create_user_name']) ? '_'.$params['order_by_create_user_name'] : '' }}" id="order_by_create_user_name" data-order="{{ $params['order_by_create_user_name'] }}" onclick="orderPage(this,'{{ $papers->url
                        ($papers->currentPage()) }}')">创建人
                        </th>
                        <th class="sorting{{ !empty($params['order_by_created_time']) ? '_'.$params['order_by_created_time'] : '' }}" id="order_by_created_time" data-order="{{ $params['order_by_created_time'] }}" onclick="orderPage(this,'{{ $papers->url($papers->currentPage())
                        }}')">创建时间
                        </th>
                        <th class="sorting{{ !empty($params['order_by_update_user_name']) ? '_'.$params['order_by_update_user_name'] : '' }}" id="order_by_update_user_name" data-order="{{ $params['order_by_update_user_name'] }}" onclick="orderPage(this,'{{ $papers->url
                        ($papers->currentPage()) }}')">修改人
                        </th>
                        <th class="sorting{{ !empty($params['order_by_updated_time']) ? '_'.$params['order_by_updated_time'] : '' }}" id="order_by_updated_time" data-order="{{ $params['order_by_updated_time'] }}" onclick="orderPage(this,'{{ $papers->url($papers->currentPage())
                        }}')">修改时间
                        </th>
                        <th width="60" class="sorting{{ !empty($params['order_by_status']) ? '_'.$params['order_by_status'] : '' }}" id="order_by_status" data-order="{{ $params['order_by_status'] }}" onclick="orderPage(this,'{{ $papers->url($papers->currentPage()) }}')">发布状态</th>
                        <th width="120">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $papers as $item => $value )
                        <tr class="text-c">
                            <td><input type="checkbox" value="{{ $value['id'] }}" name="paperId"></td>
                            <td>{{ $value['id'] }}</td>
                            <td class="text-l text-overflow">
                                <u style="cursor:pointer" class="text-primary text-overflow" style="width: 300px" onClick="showLayer('查看','{{ url('admin/paper/previewPaper').'/'.$value['id'] }}')" title="查看">
                                    {{-- 使用 strip_tags 去除字符串中的HTML标识 --}}
                                    {{ strip_tags($value['name']) }}
                                </u>
                            </td>
                            <td class="text-overflow" title="{{ $value['type'] }}">{{ $value['type'] }}</td>
                            <td title="{{ $value['getCreateUserName']['name'] }}">{{ $value['getCreateUserName']['name'] }}</td>
                            <td title="{{ $value['created_at'] }}">{{ $value['created_at'] }}</td>
                            <td title="{{ $value['getUpdateUserName']['name'] or '无'}}">{{ $value['getUpdateUserName']['name'] or '无'}}</td>
                            <td title="{{ $value['updated_at'] }}">{{ $value['updated_at'] }}</td>
                            {{--<td>21212</td>--}}
                            <td class="td-status"><span class="label label-success radius">{{ config('exam.question_status.'.$value['status'],'未知') }}</span></td>
                            <td class="f-14 td-manage">
                                @if(!$value['status'])
                                    <a style="text-decoration:none" onClick="statusChangeOne({{ $value['id'] }},1)" href="javascript:;" title="下架">
                                        <i class="Hui-iconfont Hui-iconfont-xiajia"></i>
                                    </a>
                                @else
                                    <a style="text-decoration:none" onClick="statusChangeOne({{ $value['id'] }},0)" href="javascript:;" title="发布">
                                        <i class="Hui-iconfont Hui-iconfont-fabu"></i>
                                    </a>
                                @endif
                                <a style="text-decoration:none" class="ml-5" onclick="showLayer('编辑','{{ url('admin/paper/changePaper').'/'.$value['id'] }}')" title="编辑" href="javascript:;">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                <a style="text-decoration:none" class="ml-5" onClick="deleteOne('{{ $value['id'] }}')" href="javascript:;" title="删除">
                                    <i class="Hui-iconfont Hui-iconfont-del2"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($papers->total()))
                        <tr class="text-c">
                            <td colspan="10">没有数据</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="f-l">
                <ul class="pagination">
                    <li>
                        <a style="text-decoration:none" onClick="batchDeletion('del','paperId')" href="javascript:;" title="批量删除">
                            <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                        </a>
                    </li>
                    <li>
                        <a style="text-decoration:none" onClick="batchDeletion('release','paperId')" href="javascript:;" title="批量发布">
                            <i class="Hui-iconfont Hui-iconfont-fabu"></i>
                        </a>
                    </li>
                    <li>
                        <a style="text-decoration:none" onClick="batchDeletion('underTheShelf','paperId')" href="javascript:;" title="批量下架">
                            <i class="Hui-iconfont Hui-iconfont-xiajia"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="f-l ml-20" id="DataTables_Table_0_info" role="status" aria-live="polite">
                <span class="pagination" style="line-height: 40px">
                    <span>
                        {{ $papers->currentPage() }} / {{ $papers->lastPage() }}
                    </span>，
                    <span id="total" data-total="{{ $papers->total() }}">
                        共 {{ $papers->total() }} 条
                    </span>
                </span>
            </div>
            <div class="f-r">
                {!! $papers->links() !!}
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
                                <span>删除选中试卷</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="deleteType" value="1">
                                <span>删除检索试卷</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="deleteAnyPaper()">确定</button>
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
                                <span>发布选中试卷</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="releaseType" value="1">
                                <span>发布检索试卷</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="statusChangePaper('release','releaseType',0)">确定</button>
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
                                <span>下架选中试卷</span>
                            </label>
                        </div>
                        <div class="radio-box">
                            <label class="f-l">
                                <input type="radio" name="underTheShelfType" value="1">
                                <span>下架检索试卷</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="statusChangePaper('underTheShelf','underTheShelfType',1)">确定</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        //页面加载完毕后运行,显示获得的消息
        $(function () {
            @if(Session::has('code'))
            $.Huimodalalert('{{ Session::get('message') }}',2000);
            @endif
        });0
    </script>
@endsection