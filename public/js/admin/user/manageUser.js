//页面加载完毕后运行,保证考试类型选择样式正确
$(function () {
    //初始化单选按钮
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });
});

//获取按选中条件所需要的data数据（json）
function getUsers(chooseType) {
    //选中项(0)
    if(chooseType == 0){
        var usersId = $('input[name=userId]:checked').map(function (index,elem) {
            return $(elem).val();
        }).get().join(',');

        return {
            'type' : chooseType,
            'usersId' : usersId
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
function deleteAnyUser() {
    var deleteType = $('input[name=deleteType]:checked').val();
    var data = getUsers(deleteType);

    $.ajax({
        'url' : $('#deleteUser').val(),
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
function deleteOne(userId) {
    userId = userId || '';
    $.ajax({
        'url' : $('#deleteUser').val(),
        'data' : {
            'type' : 0,
            'usersId' : userId
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
function statusChangeUser(operateType,radioName,status) {
    var Type = $('input[name='+radioName+']:checked').val();
    var data = getUsers(Type);
    data.status = status;

    $.ajax({
        'url' : $('#statusUser').val(),
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
function statusChangeOne(userId,status) {
    userId = userId || '';
    $.ajax({
        'url' : $('#statusUser').val(),
        'data' : {
            'type' : 0,
            'usersId' : userId,
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

