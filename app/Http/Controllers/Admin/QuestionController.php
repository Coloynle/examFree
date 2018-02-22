<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class QuestionController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function guest(){
        return view('admin/guest');
    }

    public function index()
    {
//        return view('admin/question/addQuestion');
    }

    /**
     *
     * 展示添加试题页面
     * @function addQuestion
     * @param $type
     * @param bool $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function addQuestion($type,$id = false)
    {
        $context = [
            'list' => ['question','addQuestion'],
            'status' => ['type' => $type,'id' => $id],
            'length' => 4
        ];
        return view('admin/question/addQuestion',['context' => $context]);
    }


    public function createQuestion(Request $request){

        $parameters = $request->all();
        //验证规则
        $vaildatedData = [];
        //自定义错误提示名称
        $vaildateName = [
            'description' => '试题描述',
            'analysis' => '试题解析',
            'option_radio' => '试题选项'
        ];
        //错误信息自定义
        $vailErrorInfo = [
            'required' => ':attribute 必填',
        ];

        //验证规则数组
        foreach ($parameters['option'] as $item => $value){
            $key = 'option.'.$item;
            $vaildatedData[$key] = "bail|required";
            $vaildateName[$key] = '选项'.$item.'描述';

        }
        $vaildatedData['description'] = "bail|required";
        $vaildatedData['analysis'] = "bail|required";
        $vaildatedData['option_radio'] = "bail|required";
//        dd($vaildatedData);

        //验证表单
        $request->validate($vaildatedData,$vailErrorInfo,$vaildateName);

        if(!isset($parameters['option'][$parameters['option_radio']])){
            echo '正确答案不存在';
            return;
        }

//        dd($request);
    }
}
