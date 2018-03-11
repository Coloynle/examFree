@extends('layouts.iframe')
@section('content')
    <div class="page-container">
        <form method="POST">
            {{ csrf_field() }}
            <input name="name" type="text" class="input-text radius" placeholder="试卷名称">
            <input name="name" type="number" min="0" class="input-text radius mt-20" disabled style="width: 10%;" placeholder="试卷总分：自动累加">
            <input name="name" type="number" min="0" class="input-text radius mt-20" style="width: 10%;" placeholder="及格分数">
            <input name="name" type="text" class="input-text radius mt-20" style="width: 10%;" placeholder="试卷分类">
            <input name="name" type="button" class="btn btn-primary radius mt-20" value="添加一道大题">
            <div id="paper_content" class="mt-20">
                <div class="panel panel-success">
                    <div class="panel-header">
                        <input name="name" type="text" class="input-text radius" placeholder="大题描述">
                    </div>
                    <div class="panel-body">
                        <div class="panel-body  border_style1">
                            <div class="panel-body">
                                选择题啦啦啦
                            </div>
                            <div class="radio-box" style="display: block">
                                <input type="radio" id="option_A">
                                <label for="option_A">选项A</label>
                            </div>
                            <div class="radio-box" style="display: block">
                                <input type="radio" id="option_B">
                                <label for="option_B">选项B</label>
                            </div>
                            <div class="radio-box" style="display: block">
                                <input type="radio" id="option_C">
                                <label for="option_C">选项C</label>
                            </div>
                            <div class="radio-box" style="display: block">
                                <input type="radio" id="option_D">
                                <label for="option_D">选项D</label>
                            </div>
                        </div>
                        <input name="name" type="button" class="btn btn-primary radius" value="添加一道试题">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(function () {
            $('.radio-box input').iCheck({
                checkboxClass: 'icheckbox-grey t-2',
                radioClass: 'iradio-grey t-2',
                increaseArea: '20%'
            })
        })
    </script>
@endsection