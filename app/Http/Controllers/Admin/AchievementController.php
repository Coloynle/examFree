<?php

namespace App\Http\Controllers\Admin;

use App\Exam;
use App\UserAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AchievementController extends Controller
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
     * 获得params数组
     *
     * @function initParams
     * @return array
     * @author CJ
     */
    private function initParams()
    {
        //初始化params
        return [
            'id' => Input::get('id', ''),
            'name' => Input::get('name', ''),
            'exam_time_start' => Input::get('exam_time_start', ''),
            'exam_time_end' => Input::get('exam_time_end', ''),
            'create_user_name' => Input::get('create_user_name', ''),
            'created_time_start' => Input::get('created_time_start', ''),
            'created_time_end' => Input::get('created_time_end', ''),
            'order_by_created_time' => Input::get('order_by_created_time', ''),
        ];
    }

    public function manualEvaluationExam(Request $request){
        $userAnswer = new UserAnswer();

        $examResultPaginate = $userAnswer->getExamPaginate(self::initParams());


//        dd($examResultPaginate);
        return view('admin/achievement/manualEvaluationExam',[
            'examResultPaginate' => $examResultPaginate,
            'params' => self::initParams()
        ]);
    }
}
