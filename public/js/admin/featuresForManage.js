/**
 *  所有管理页面通用JS
 */

//AJAX TOKEN初始化
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//自定义选择按钮事件
function chooseButton(that,hidden) {
    $(that).siblings('a').removeClass('btn-primary');
    $(that).siblings('a').removeClass('btn-default');
    $(that).siblings('a').addClass('btn-default');
    $(that).removeClass('btn-default');
    $(that).removeClass('btn-primary');
    $(that).addClass('btn-primary');
    hidden.val($(that).data('value'));
}

//重置搜索表单,并且提交表单
function resetFrom() {
    $('#searchFrom input[type=text]').each(function () {
        $(this).val('');
    });
    $('input[name=type]').val('');
    $('input[name=status]').val('');
    $('#searchFrom').submit();
}

//排序分页方法 刷新页面
function orderPage(that, href) {
    var order = $(that).data('order');
    var key = $(that).attr('id');
    if (order == '') {
        $(that).data('order', 'desc');
        $(that).attr('class', 'sorting_desc');
        window.location.href = href + '&' + key + '=desc';
    } else if (order == 'desc') {
        $(that).data('order', 'asc');
        $(that).attr('class', 'sorting_asc');
        window.location.href = href + '&' + key + '=asc';
    } else if (order == 'asc') {
        $(that).data('order', '');
        $(that).attr('class', 'sorting');
        window.location.href = href + '&' + key + '=';
    }
}

//展示批量操作弹出层
function batchDeletion(operateType,idName) {

    if($('#total').data('total') == 0){
        $.Huimodalalert('没有可以操作的数据',2000);
    }else{
        if($('input[name='+idName+']:checked').length == 0){
            $('#choose-'+operateType).attr('disabled','true');
            $('#modal-'+ operateType).find('.radio-box').eq(0).iCheck('uncheck');
        }else {
            $('#choose-'+operateType).removeAttr('disabled');
        }
        $('#modal-'+ operateType).modal("show");
    }
}

//预览试题方法
function showLayer(title,url) {
    var index = layer.open({
        type: 2,
        title: title,
        content: url,
        move : false,
        yes : function (layero,index) {
        }
    });
    layer.full(index);
}
