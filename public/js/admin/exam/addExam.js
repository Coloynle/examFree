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

    var start_time_type = $('input[name=start_time_type]').val() || '';
    $('#start_time_type a').each(function () {
        if ($(this).data('value').toString() === start_time_type) {
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
            papersId = compareArray(chooseIdArray, nowIdArray).join(',');

            paperAdd(papersId);
            if ($('#paper_id').val() == '')
                $('#paper_id').val(papersId);
            else {
                if (papersId != '')
                    $('#paper_id').val($('#paper_id').val() + ',' + papersId);
            }
            layer.close(index);
        },
        area: ['1500px', '700px'],
        content: addPaperId
    });
}

//通过papersId字符串（eg:1,2,3）发送AJAX请求获得试卷数据并加入到表格
function paperAdd(papersId) {
    $.ajax({
        'type': 'POST',
        'data': {
            'papersId': papersId,
        },
        'url': getPaperId,
        'dataType': 'JSON',
        'success': function (data) {
            $.each(data, function (key, paper) {
                var trHtml = "<tr class='text-c' id='paperId_" + paper.id + "'>" +
                    "<td><input type='checkbox' value='" + paper.id + "' name='paperId'></td>" +
                    "<td>" + paper.id + "</td>" +
                    "<td class='text-l text-overfpaper_idlow'>" +
                    "<u style='cursor:pointer' class='text-primary text-overflow' style='width: 400px' onClick='showLayer(\"查看\",\"" + showPaperUrl + paper.id + "\")' title='查看'>" + paper.name + "</u>" +
                    "</td>" +
                    "<td class='text-overflow' title='" + paper.type + "'>" + paper.type + "</td>" +
                    "<td title='" + paper.total_score + "'>" + paper.total_score + "</td>" +
                    "<td title='" + paper.passing_score + "'>" + paper.passing_score + "</td>" +
                    "<td>" +
                    "<a style='text-decoration:none' class='ml-5' onClick='paperRemove(\"" + paper.id + "\")' href='javascript:;' title='删除'>" +
                    "<i class='Hui-iconfont Hui-iconfont-del2'></i>" +
                    "</a>" +
                    "</td>" +
                    "</tr>";
                $(tbodyDom).before($(trHtml));
                tbodyDom.data('count', parseInt(tbodyDom.data('count')) + 1);
            });
            if (tbodyDom.data('count') > 0) {
                $('#emptyDom').hide();
            } else {
                $('#emptyDom').show();
            }
        }
    });
}

//对比数组1和数组2, 将数组1中和数组2重复的部分去除并返回剩下的数组
function compareArray(arr1, arr2) {
    var temp = [];
    var tempArray = [];
    for (var i = 0; i < arr2.length; i++) {
        temp[arr2[i]] = true;
    }
    for (var j = 0; j < arr1.length; j++) {
        if (!temp[arr1[j]])
            tempArray.push(arr1[j]);
    }
    return tempArray;
}

//移除单个试卷
function paperRemove(paperId) {
    $("#paperId_" + paperId).remove();
    tbodyDom.data('count', parseInt(tbodyDom.data('count')) - 1);
    if (tbodyDom.data('count') > 0) {
        $('#emptyDom').hide();
    } else {
        $('#emptyDom').show();
    }
}

//批量移除试卷
function papersRemove() {
    $('table input[name=paperId]:checked').map(function (index, elem) {
        paperRemove($(elem).val());
    })
}