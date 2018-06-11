<?php

namespace App\Http\Controllers\Admin;

use App\Exam;
use App\ExamResult;
use App\Paper;
use App\Question;
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
        $context = [
            'status' => ['breadcrumbTop' => false, 'score' => false]      //用户ID 和 类型
        ];
        $userAnswer = new UserAnswer();
        $examResultPaginate = $userAnswer->getEvaluationPaginate(self::initParams());

//        dd($examResultPaginate);
        return view('admin/achievement/manualEvaluationExam',[
            'examResultPaginate' => $examResultPaginate,
            'params' => self::initParams(),
            'context' => $context,
        ]);
    }

    public function achievementDetails(Request $request){
        $context = [
            'status' => ['breadcrumbTop' => false, 'score' => true]      //用户ID 和 类型
        ];
        $userAnswer = new UserAnswer();
        $examResultPaginate = $userAnswer->getDetailsPaginate(self::initParams());
//        dd($examResultPaginate);
        return view('admin/achievement/manualEvaluationExam',[
            'examResultPaginate' => $examResultPaginate,
            'params' => self::initParams(),
            'context' => $context,
        ]);
    }

    public function startEvaluation($id = ''){
        $userAnswer = new UserAnswer();
        $userAnswer = $userAnswer->getPaperOne($id);
        $result = json_decode($userAnswer['result'], true);

        $questions = array_keys($result);

        $question = new Question();
        $question = $question->whereIn('id',$questions);
        $question = $question->where('type','=','ShortAnswer')->get()->toArray();

//        dd($question);

        $paper = new Paper();
        $paper = $paper->find($userAnswer['paper_id']);
        $score = unserialize($paper['content']);

        foreach ($question as $key => $value){
            $question[$key]['answer_info'] = unserialize($question[$key]['answer_info']);
            foreach ($score as $item => $val){
                if(isset($val[$value['id']])){
                    $question[$key]['score'] = $val[$value['id']];
                    $question[$key]['user_answer'] = $result[$value['id']];
                    break;
                }
            }
        }
//        dd($question);
        return view('admin/achievement/startEvaluation',[
            'question' => $question,
            'id' => $id
        ]);
    }

    public function saveEvaluation($id = ''){
        $score = Input::get();
        $examResult = ExamResult::find($id);
        $examResult->score = $examResult['score'] + $score['score'];
        $TFsuccess = $examResult->save();

        $userAnswer = UserAnswer::find($id);
        $userAnswer->manual_evaluation = 0;
        $userAnswer->save();

        if($TFsuccess){
            return [
                'code' => 1,
                'message' => '评卷成功'
            ];
        }else{
            return [
                'code' => -1,
                'message' => '评卷失败'
            ];
        }
    }
}
