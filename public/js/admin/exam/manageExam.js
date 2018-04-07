//页面加载完毕后运行,保证考试类型选择样式正确
$(function () {
    //考试状态搜索条件
    var status = $('input[name=status]').val() || '';
    $('#examStatus a').each(function () {
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
    var type = $('input[name=type]').val() || '';
    if(type == '0'){
        $('#apply_min').hide();
        $('#apply_max').hide();
    }
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

    //初始化单选按钮
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });
});

//获取按选中条件所需要的data数据（json）
function getExams(chooseType) {
    //选中项(0)
    if(chooseType == 0){
        var examsId = $('input[name=examId]:checked').map(function (index,elem) {
            return $(elem).val();
        }).get().join(',');

        return {
            'type' : chooseType,
            'examsId' : examsId
        };
    }
    //检索条件(1)
    else if(chooseType == 1){
        return {
            'params' : $('#params').val(),
            'type' : chooseType
        };
    }
    //如果没有选择
    else{
        $.Huimodalalert('请选择一种方式',2000);
        return false;
    }
}

//批量删除弹出层确定按钮事件 （发送AJAX请求软删除考试）
function deleteAnyExam() {
    var deleteType = $('input[name=deleteType]:checked').val();
    var data = getExams(deleteType);

    $.ajax({
        'url' : $('#deleteExam').val(),
        'data' : data,
        'type' : 'POST',
        'success' : function (data) {
            $('#modal-del').modal("hide");
            if(data.code == 0){
                $.Huimodalalert(data.message,2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                },2000);
            }else{
                $.Huimodalalert(data.message,2000);
            }
        }
    });
}

//删除单一考试
function deleteOne(examId) {
    examId = examId || '';
    $.ajax({
        'url' : $('#deleteExam').val(),
        'data' : {
            'type' : 0,
            'examsId' : examId
        },
        'type' : 'POST',
        'success' : function (data) {
            $('#modal-del').modal("hide");
            if(data.code == 0){
                $.Huimodalalert(data.message,2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                },2000);
            }else{
                $.Huimodalalert(data.message,2000);
            }
        }
    });
}

//批量上架下架弹出层确定按钮事件 （发送AJAX请求软删除考试）
function statusChangeExam(operateType,radioName,status) {
    var Type = $('input[name='+radioName+']:checked').val();
    var data = getExams(Type);
    data.status = status;

    $.ajax({
        'url' : $('#statusExam').val(),
        'data' : data,
        'type' : 'POST',
        'success' : function (data) {
            $('#modal-' + operateType).modal("hide");
            if(data.code == 0){
                $.Huimodalalert(data.message,2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                },2000);
            }else{
                $.Huimodalalert(data.message,2000);
            }
        }
    });
}

//上下架单一考试
function statusChangeOne(examId,status) {
    examId = examId || '';
    $.ajax({
        'url' : $('#statusExam').val(),
        'data' : {
            'type' : 0,
            'examsId' : examId,
            'status' : status,
        },
        'type' : 'POST',
        'success' : function (data) {
            $('#modal-del').modal("hide");
            if(data.code == 0){
                $.Huimodalalert(data.message,2000);
                setTimeout(function () {
                    location.replace($('#currentPage').val());
                },2000);
            }else{
                $.Huimodalalert(data.message,2000);
            }
        }
    });
}

