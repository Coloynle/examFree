<?php

namespace App\Http\Controllers\Admin;

use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * 中间件验证是否登录
     *
     * PaperController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 添加考试界面
     *
     * @function addExam
     * @param bool $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function addExam($id = false){
        $context = [
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //类型 和 试卷ID
        ];
        return view('admin/exam/addExam',['context' => $context]);
    }

    public function createExam(Request $request){

        //将要保存的数据
        $parameter = $request->all();
        //作为验证的数据
        $parameters = $request->all();
        $parameters['description'] = strip_tags($parameters['description']);
        //验证规则
        $vaildatedData = [];
        //自定义错误提示名称
        $vaildateName = [
            'examName' => '考试名称',
            'type' => '考试类型',
            'sort' => '考试分类',
            'exam_time_start' => '考试开始时间',
            'exam_time_end' => '考试结束时间',
            'apply_time_start' => '报名开始时间',
            'apply_time_end' => '报名结束时间',
            'start_time_type' => '考试时间计算类别',
            'duration' => '考试时长',
            'description' => '考试描述',
            'paper_id' => '试卷',
        ];
        //错误信息自定义
        $vailErrorInfo = [
            'required' => ':attribute 必填',
            'max' => ':attribute 长度不可大于10000',
        ];

        $vaildatedData['examName'] = "bail|required|max:200";
        $vaildatedData['type'] = "bail|required";
        $vaildatedData['sort'] = "bail|required";
        $vaildatedData['exam_time_start'] = "bail|required";
        $vaildatedData['exam_time_end'] = "bail|required";
        if($parameters['type'] == 1) {
            $vaildatedData['apply_time_start'] = "bail|required";
            $vaildatedData['apply_time_end'] = "bail|required";
        }
        $vaildatedData['start_time_type'] = "bail|required";
        $vaildatedData['duration'] = "bail|required";
        $vaildatedData['description'] = "bail|required|max:30000";
        $vaildatedData['paper_id'] = "bail|required";

        $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();

        $save = self::saveExam($parameter);
        if($save){
            if(empty($parameters['examId'])){
                $message = [
                    'code' => '1',
                    'message' => '添加考试成功'
                ];
            }else{
                $message = [
                    'code' => '2',
                    'message' => '修改考试成功'
                ];
            }
            return redirect('/admin/exam/addExam/')->with($message);
        }else{
            return redirect('/admin/exam/addExam/')->with([
                'code' => '0',
                'message' => '考试保存失败'
            ]);
        }
    }

    /**
     * 保存试题到数据库 (或更新)
     *
     * @function saveQuestion
     * @param $parameters
     * @return bool
     * @author CJ
     */
    public function saveExam($parameters)
    {
        if (!empty($parameters['examId'])) {
            $Exam = Exam::find($parameters['examId']);
            if (empty($Exam)) {
                $Exam = new Exam;
            } else {
                $Exam->update_user_id = Auth::guard('admin')->user()->id;
            }

        } else {
            $Exam = new Exam;
            $Exam->create_user_id = Auth::guard('admin')->user()->id;
            $Exam->update_user_id = 0;
        }

        //插入试题到数据库
        $Exam->name = $parameters['examName'];
        $Exam->type = $parameters['type'];
        $Exam->sort = $parameters['sort'];
        $Exam->exam_time_start = $parameters['exam_time_start'];
        $Exam->exam_time_end = $parameters['exam_time_end'];
        $Exam->apply_time_start = $parameters['apply_time_start'];
        $Exam->apply_time_end = $parameters['apply_time_end'];
        $Exam->start_time_type = $parameters['start_time_type'];
        $Exam->duration = $parameters['duration'];
        $Exam->description = $parameters['description'];
        $Exam->paper_id = $parameters['paper_id'];
        return $Exam->save();
    }

}
