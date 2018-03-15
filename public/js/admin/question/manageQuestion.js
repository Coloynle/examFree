//页面加载完毕后运行,保证试题类型选择样式正确
$(function () {
    //试题类型搜索条件
    var type = $('input[name=type]').val() || '';
    $('#questionType a').each(function () {
        if ($(this).data('value') == type) {
            $(this).removeClass('btn-default');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-primary');
        } else {
            $(this).removeClass('btn-default');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-default');
        }
    });

    //试题类型搜索条件
    var status = $('input[name=status]').val() || '';
    $('#questionStatus a').each(function () {
        if ($(this).data('value').toString() === status) {
            $(this).removeClass('btn-default');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-primary');
        } else {
            $(this).removeClass('btn-default');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-default');
        }
    });

    //初始化单选按钮
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });
});

//AJAX TOKEN初始化
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//获取按选中条件所需要的data数据（json）
function getQuestions(chooseType) {
    //选中项(0)
    if (chooseType == 0) {
        var questionsId = $('input[name=questionId]:checked').map(function (index, elem) {
            return $(elem).val();
        }).get().join(',');

        return {
            'type': chooseType,
            'questionsId': questionsId
        };
    }
    //检索条件(1)
    else if (chooseType == 1) {
        return {
            'params': $('#params').val(),
            'type': chooseType
        };
    }
    //如果没有选择
    else {
        $.Huimodalalert('请选择一种方式', 2000);
        return false;
    }
}

//批量删除弹出层确定按钮事件 （发送AJAX请求软删除试题）
function deleteAnyQuestion() {
    var deleteType = $('input[name=deleteType]:checked').val();
    var data = getQuestions(deleteType);

    $.ajax({
        'url': $('#deleteQuestion').val(),
        'data': data,
        'type': 'POST',
        'success': function (data) {
            $('#modal-del').modal("hide");
            if (data.code == 0) {
                $.Huimodalalert(data.message, 2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                }, 2000);
            } else {
                $.Huimodalalert(data.message, 2000);
            }
        }
    });
}

//删除单一试题
function deleteOne(questionId) {
    questionId = questionId || '';
    $.ajax({
        'url': $('#deleteQuestion').val(),
        'data': {
            'type': 0,
            'questionsId': questionId
        },
        'type': 'POST',
        'success': function (data) {
            $('#modal-del').modal("hide");
            if (data.code == 0) {
                $.Huimodalalert(data.message, 2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                }, 2000);
            } else {
                $.Huimodalalert(data.message, 2000);
            }
        }
    });
}

//批量上架下架弹出层确定按钮事件 （发送AJAX请求软删除试题）
function statusChangeQuestion(operateType, radioName, status) {
    var Type = $('input[name=' + radioName + ']:checked').val();
    var data = getQuestions(Type);
    data.status = status;

    $.ajax({
        'url': $('#statusQuestion').val(),
        'data': data,
        'type': 'POST',
        'success': function (data) {
            $('#modal-' + operateType).modal("hide");
            if (data.code == 0) {
                $.Huimodalalert(data.message, 2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                }, 2000);
            } else {
                $.Huimodalalert(data.message, 2000);
            }
        }
    });
}

//上下架单一试题
function statusChangeOne(questionId, status) {
    questionId = questionId || '';
    $.ajax({
        'url': $('#statusQuestion').val(),
        'data': {
            'type': 0,
            'questionsId': questionId,
            'status': status,
        },
        'type': 'POST',
        'success': function (data) {
            $('#modal-del').modal("hide");
            if (data.code == 0) {
                $.Huimodalalert(data.message, 2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                }, 2000);
            } else {
                $.Huimodalalert(data.message, 2000);
            }
        }
    });
}
