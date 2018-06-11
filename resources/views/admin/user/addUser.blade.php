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

        function getPath(obj)
        {
            if(obj)
            {
                if (window.navigator.userAgent.indexOf("MSIE")>=1)
                {
                    obj.select();
                    return document.selection.createRange().text;
                }
                else if(window.navigator.userAgent.indexOf("Firefox")>=1)
                {
                    if(obj.files)
                    {
                        return obj.files.item(0).getAsDataURL();
                    }
                    return obj.value;
                }
                return obj.value;
            }
        }
    </script>
    <script type="text/javascript" src="{{ asset('js/admin/exam/addExam.js') }}"></script>

    <div class="page-container">
        <form method="POST" action="{{ url('admin/user/createUser') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input name="userId" type="hidden" class="input-text radius"  value="{{ old('userId') }}">
            <span class="c-error">{{ $errors->first('userName') }}</span>
            <input name="userName" type="text" class="input-text radius mb-15" placeholder="用户名"  value="{{ old('userName') }}">
            <span class="c-error">{{ $errors->first('userEmail') }}</span>
            <input name="userEmail" type="text" class="input-text radius mb-15" placeholder="Email"  value="{{ old('userEmail') }}">
            <span class="c-error">{{ $errors->first('userPassword') }}</span>
            <input name="userPassword" type="password" class="input-text radius mb-15" placeholder="密码" value="{{ old('userPassword') }}">
            <input id="saveUser" type="submit" class="btn btn-success-outline radius btn-block mt-10" value="保存用户">
        </form>
    </div>
@endsection