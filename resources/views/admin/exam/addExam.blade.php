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
        var tbodyDom = '';
        $(function () {
            var type = $('input[name=type]').val() || '';
            $('#examType a').each(function () {
                if ($(this).data('value').toString() === type) {
                    $(this).removeClass('btn-default');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-primary');
                } else {
                    $(this).removeClass('btn-default');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-default');
                }
            });
        });

        /* Ueditor创建 */
        function getUeditor(id, content) {
            content = content || '';
            var ue = UE.getEditor(id, {
                initialFrameWidth: '100%'
            });
            //防止提交表单时，不提交没有使用过的编辑器
            ue.addListener("ready", function () {
                // editor准备好之后才可以使用
                ue.setContent(content);
            });
        }

        //选择试卷弹出层
        function paperLayer(that) {
            layer.open({
                type: 2,
                title: '选择试卷',
                shadeClose: false,
                shade: 0.1,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                yes: function (index, layero) {
                    tbodyDom = $(that).parent().find('tbody');

                    var body = layer.getChildFrame('body', index);

                    var papersId = body.find('input[name=paperId]:checked').map(function (index, elem) {
                        return $(elem).val();
                    }).get().join(',');

                    var chooseIdArray = papersId.split(',');
                    var nowIdArray = $('#paper_id').val().split(',');
                    papersId = compareArray(chooseIdArray,nowIdArray).join(',');

                    paperAdd(papersId);
                    if($('#paper_id').val() == '')
                        $('#paper_id').val(papersId);
                    else {
                        if(papersId != '')
                            $('#paper_id').val($('#paper_id').val() + ',' + papersId);
                    }
                    layer.close(index);
                },
                area: ['1500px', '700px'],
                content: '{{ url('admin/paper/managePaper/true') }}'
            });
        }

        //通过papersId字符串（eg:1,2,3）发送AJAX请求获得试卷数据并加入到表格
        function paperAdd(papersId){
            $.ajax({
                'type': 'POST',
                'data': {
                    'papersId': papersId,
                },
                'url': '{{ url('admin/paper/getPaperById') }}',
                'dataType': 'JSON',
                'success': function (data) {
                    $.each(data, function (key, paper) {
                        var trHtml = "<tr class='text-c' id='paperId_"+paper.id+"'>" +
                            "<td><input type='checkbox' value='"+ paper.id +"' name='paperId'></td>" +
                            "<td>"+ paper.id +"</td>" +
                            "<td class='text-l text-overfpaper_idlow'>" +
                            "<u style='cursor:pointer' class='text-primary text-overflow' style='width: 400px' onClick='showLayer(\"查看\",\""+ showPaperUrl + paper.id +"\")' title='查看'>"+ paper.name +"</u>" +
                            "</td>" +
                            "<td class='text-overflow' title='"+ paper.type +"'>"+ paper.type +"</td>" +
                            "<td title='"+ paper.total_score +"'>"+ paper.total_score +"</td>" +
                            "<td title='"+ paper.passing_score +"'>"+ paper.passing_score +"</td>" +
                            "<td>" +
                            "<a style='text-decoration:none' class='ml-5' onClick='paperRemove(\""+ paper.id +"\")' href='javascript:;' title='删除'>" +
                            "<i class='Hui-iconfont Hui-iconfont-del2'></i>" +
                            "</a>" +
                            "</td>" +
                            "</tr>";
                        $(tbodyDom).before($(trHtml));
                        tbodyDom.data('count', parseInt(tbodyDom.data('count'))+1);
                    });
                    if (tbodyDom.data('count') > 0){
                        $('#emptyDom').hide();
                    }else{
                        $('#emptyDom').show();
                    }
                }
            });
        }

        //对比数组1和数组2, 将数组1中和数组2重复的部分去除并返回剩下的数组
        function compareArray(arr1,arr2) {
            console.log(arr1,arr2);
            var temp = [];
            var tempArray = [];
            for(var i=0;i<arr2.length;i++){
                temp[arr2[i]] = true;
            }
            for(var j=0;j<arr1.length;j++){
                if(!temp[arr1[j]])
                    tempArray.push(arr1[j]);
            }
            console.log(tempArray,temp);
            return tempArray;
        }

        //移除单个试卷
        function paperRemove(paperId){
            $("#paperId_"+ paperId).remove();
            tbodyDom.data('count', parseInt(tbodyDom.data('count'))-1);
            if (tbodyDom.data('count') > 0){
                $('#emptyDom').hide();
            }else{
                $('#emptyDom').show();
            }
        }

        //批量移除试卷
        function papersRemove(){
            $('table input[name=paperId]:checked').map(function (index, elem) {
                paperRemove($(elem).val());
            })
        }

        function choosePapers() {

        }
    </script>

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