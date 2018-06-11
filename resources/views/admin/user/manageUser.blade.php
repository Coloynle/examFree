@extends('layouts.iframe')
@section('content')
    {{-- 日期插件 --}}
    <script type="text/javascript" src="{{ asset('h-ui/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/featuresForManage.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/user/manageUser.js') }}"></script>
    {{-- 表格插件 --}}
    {{--<script type="text/javascript" src="lib/datatables/1.10.0/jquery.dataTables.min.js"></script>--}}
    {{-- laypage插件 --}}
    {{--<script type="text/javascript" src="lib/laypage/1.2/laypage.js"></script>--}}
    <link href="{{ asset('css/addClass.css') }}" rel="stylesheet" type="text/css"/>
    <input type="hidden" id="deleteUser" value="{{ url('admin/user/deleteUser/') }}">
    <input type="hidden" id="currentPage" value="{{ $users->url($users->currentPage()) }}">
    <input type="hidden" id="params" value='{!! serialize($params) !!}'>
    <div class="page-container">
        <div class="mt-10">
            <form method="POST" action="{{ url('admin/user/manageUser/') }}" id="searchFrom">
                <div class="cl">
                    {{ csrf_field() }}
                    <input type="text" name="id" value="{{ $params['id'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 100px" placeholder="用户ID">
                    <input type="text" name="name" value="{{ $params['name'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="用户名称">
                    <input type="text" name="email" value="{{ $params['email'] }}" class="input-text radius f-l mr-10 mt-10" style="width: 200px" placeholder="Email">
                    <input type="submit" class="btn btn-primary radius f-l mr-10 mt-10" value="搜索">
                    <input type="button" class="btn btn-primary radius f-l mr-10 mt-10" onclick="resetFrom();" value="重置">
                </div>
            </form>
        </div>
        <div class="mt-20 cl">
            <div id="userPage">
                <table class="table table-border table-bordered table-bg table-hover table-sort table-responsive dataTable" style="table-layout:fixed;">
                    <thead>
                    <tr class="text-c">
                        <th width="25"><input type="checkbox" name="" value=""></th>
                        <th width="25" class="sorting{{ !empty($params['order_by_id']) ? '_'.$params['order_by_id'] : '' }}" id="order_by_id" data-order="{{ $params['order_by_id'] }}" onclick="orderPage(this,'{{ $users->url($users->currentPage()) }}')">ID</th>
                        <th width="170" class="sorting{{ !empty($params['order_by_name']) ? '_'.$params['order_by_name'] : '' }}" id="order_by_name" data-order="{{ $params['order_by_name'] }}" onclick="orderPage(this,'{{ $users->url($users->currentPage()) }}')">用户名</th>
                        <th width="80" class="sorting{{ !empty($params['order_by_email']) ? '_'.$params['order_by_email'] : '' }}" id="order_by_email" data-order="{{ $params['order_by_email'] }}" onclick="orderPage(this,'{{ $users->url($users->currentPage()) }}')">Email</th>
                        <th width="90">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $users as $item => $value )
                        <tr class="text-c">
                            <td><input type="checkbox" value="{{ $value['id'] }}" name="userId"></td>
                            <td>{{ $value['id'] }}</td>
                            <td class="text-l text-overflow">
                                <u class="text-primary text-overflow" style="width: 170px" title="{{ strip_tags($value['name']) }}">
                                    {{-- 使用 strip_tags 去除字符串中的HTML标识 --}}
                                    {{ strip_tags($value['name']) }}
                                </u>
                            </td>
                            <td class="text-l text-overflow">
                                <u class="text-primary text-overflow" style="width: 80px" title="{{ strip_tags($value['email']) }}">
                                    {{-- 使用 strip_tags 去除字符串中的HTML标识 --}}
                                    {{ strip_tags($value['email']) }}
                                </u>
                            </td>
                            <td class="f-14 td-manage">
                                <a style="text-decoration:none" class="ml-5" onclick="showLayer('编辑','{{ url('admin/user/changeUser').'/'.$value['id'] }}')" title="编辑" href="javascript:;">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                <a style="text-decoration:none" class="ml-5" onClick="deleteOne('{{ $value['id'] }}')" href="javascript:;" title="删除">
                                    <i class="Hui-iconfont Hui-iconfont-del2"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($users->total()))
                        <tr class="text-c">
                            <td colspan="5">没有数据</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="f-l">
                <ul class="pagination">
                    <li>
                        <a style="text-decoration:none" onClick="batchDeletion('del','userId')" href="javascript:;" title="批量删除">
                            <i class="Hui-iconfont Hui-iconfont-del2"> </i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="f-l ml-20" id="DataTables_Table_0_info" role="status" aria-live="polite">
                <span class="pagination" style="line-height: 40px">
                    <span>
                        {{ $users->currentPage() }} / {{ $users->lastPage() }}
                    </span>，
                    <span id="total" data-total="{{ $users->total() }}">
                        共 {{ $users->total() }} 条
                    </span>
                </span>
            </div>
            <div class="f-r">
                {!! $users->links() !!}
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
                    <button class="btn btn-primary" onclick="deleteAnyUser()">确定</button>
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
                    <button class="btn btn-primary" onclick="statusChangeUser('release','releaseType',0)">确定</button>
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
                    <button class="btn btn-primary" onclick="statusChangeUser('underTheShelf','underTheShelfType',1)">确定</button>
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