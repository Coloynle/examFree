@extends('layouts.iframe')
@section('content')
    <div class="page-container">
        <form method="POST">
            {{ csrf_field() }}
            <input name="paperName" type="text" class="input-text radius" placeholder="试卷名称">
            <input name="paperScore" type="number" min="0" class="input-text radius mt-20" value="0" disabled style="width: 10%;" placeholder="试卷总分：自动累加">
            <input name="paperPass" type="number" min="0" class="input-text radius mt-20" style="width: 10%;" placeholder="及格分数">
            <input name="paperType" type="text" class="input-text radius mt-20" style="width: 10%;" placeholder="试卷分类">
            <div id="paper_content" class="mt-20">
            </div>
            <div class="text-c">
                <input name="name" type="button" class="btn btn-primary radius mt-20" onclick="addMainQuestion();" value="添加一道大题">
                <input name="name" type="button" class="btn btn-primary radius mt-20 ml-50" onclick="savePaper();" value="保存试卷">
            </div>
        </form>
    </div>
    <script>
        //初始化单选按钮和多选按钮
        $(function () {
            $('.radio-box input').iCheck({
                checkboxClass: 'icheckbox-grey t-2',
                radioClass: 'iradio-grey t-2',
                increaseArea: '20%'
            })
        });

        //AJAX TOKEN初始化
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //添加一道大题
        function addMainQuestion() {
            var divHtml = "<div class=\"panel panel-success mt-10\">\n" +
                "<div class=\"panel-header cl\">" +
                "<input name=\"name\" type=\"text\" class=\"input-text radius\" style=\"width: 90%;\" placeholder=\"大题描述\">" +
                "<a style=\"text-decoration:none\" class=\"btn btn-danger radius f-r ml-5\" onClick=\"delMainQuestion(this);\" href=\"javascript:;\" title=\"删除\">" +
                "<i class=\"Hui-iconfont Hui-iconfont-del2\"> </i>\n" +
                "</a>" +
                "<input name=\"eachNum\" type=\"number\" min=\"0\" class=\"input-text radius f-r\" style=\"width: 5%;\" placeholder=\"小题分数\">" +
                "</div>" +
                "<div class=\"panel-body\">" +
                "<input name=\"name\" type=\"button\" class=\"btn btn-primary radius\" onclick='secondaryQuestionLayer(this);' value=\"选择试题\">" +
                "</div>" +
                "</div>";
            $(divHtml).appendTo($('#paper_content'));
        }

        //删除一道大题
        function delMainQuestion(that) {
            $(that).parents('.panel-success').remove();
            totalScoreAuto();
        }

        //添加小题弹出层
        function secondaryQuestionLayer(that) {
            layer.open({
                type: 2,
                title: '选择试题',
                shadeClose: false,
                shade: 0.1,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                yes: function (index, layero) {
                    var body = layer.getChildFrame('body', index);

                    var questionsId = body.find('input[name=questionId]:checked').map(function (index, elem) {
                        return $(elem).val();
                    }).get().join(',');

                    $.ajax({
                        'type': 'POST',
                        'data': {
                            'questionsId': questionsId,
                        },
                        'url': '{{ url('admin/question/getQuestionById') }}',
                        'dataType': 'JSON',
                        'success': function (data) {
                            $.each(data, function (key,question) {
                                //获取小题题号
                                var questionNum = ++$(that).parent().find('span[name=questionNum]').length;
                                var divHtml = "<div class=\"panel-body\">" +
                                    "<div class=\"cl\">" +
                                    "<span class=\"badge badge-secondary radius mt-5\" name=\"questionNum\">"+ questionNum +"</span>" +
                                    "<span class=\"label label-default radius mt-5 ml-10\">" + question.type + "</span>\n" +
                                    "<a style=\"text-decoration:none\" class=\"badge badge-danger radius mt-5 f-r\" onClick=\"delSecondaryQuestion(this);\" href=\"javascript:;\" title=\"删除\">" +
                                    "<i class=\"Hui-iconfont Hui-iconfont-del2\"> </i>\n" +
                                    "</a>" +
                                    "<input type=\"number\" min=\"0\" class=\"input-text radius f-r mr-15\" onblur=\"totalScoreAuto();\" value=\""+ $(that).parent().parent().find('input[name=eachNum]').val() +"\" name=\"" + question.id + "\" style=\"width: 100px\" placeholder=\"试题分数\">" +
                                    "</div>" +
                                    "<div class=\"panel-body\">" +
                                    question.description +
                                    "</div>" +
                                    "</div>";
                                $(that).before($(divHtml));
                            });
                            totalScoreAuto();
                        }
                    });
                },
                area: ['1500px', '700px'],
                content: '{{ url('admin/question/manageQuestion/true') }}'
            });
        }

        //删除一道小题
        function delSecondaryQuestion(that) {
            //获取父节点
            var parentDom = $(that).parent().parent();
            //获取当前父节点后全部兄弟节点，并改变题号
            parentDom.nextAll().each(function () {
               $(this).find('span[name=questionNum]').html($(this).find('span[name=questionNum]').html()-1);
            });
            //移除节点
            parentDom.remove();
            totalScoreAuto();
        }

        //自动累加试卷总分
        function totalScoreAuto() {
            var totalScore = 0;
            $('.panel-body input[type=number]').each(function () {
                totalScore += Number($(this).val());
            });
            $('input[name=paperScore]').val(totalScore);
        }

        //保存试卷
        function savePaper() {
            var data = {};
            data.content = {};
            data.name = $('input[name=paperName]').val();
            data.total_score = $('input[name=paperScore]').val();
            data.passing_score = $('input[name=paperPass]').val();
            data.type = $('input[name=paperType]').val();

            $('#paper_content').children().each(function () {
               var key = $(this).find('input[name=name]').val();
                data.content[key] = {};
               $(this).find('input[type=number]').each(function () {
                   if($(this).attr('name') != 'eachNum'){
                       data.content[key][$(this).attr('name')] = $(this).val();
                   }
               })
            });

            $.ajax({
                'type': 'POST',
                'data': data,
                'url': '{{ url('admin/paper/savePaper') }}',
                'dataType': 'JSON',
                'success': function (data) {
                    window.location.reload();
                    Huimodalalert(data.message,2000);
                }
            })
            // console.log($('#paper_content').children());
        }
    </script>
@endsection