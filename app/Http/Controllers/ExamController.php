<?php

namespace App\Http\Controllers;

use App\Exam;
use App\ExamResult;
use App\Http\Controllers\Admin\QuestionController;
use App\Paper;
use App\Question;
use App\UserAnswer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ExamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $exams = new Exam();
        $exam = $exams->pageResult(['status' => '0'], 9);
        return view('exam/index', ['exams' => $exam]);
    }

    /**
     * 展示考试的详细信息
     *
     * @function showExam
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function showExam($id = null)
    {
        $exams = new Exam();
        $exam = $exams->getExam(['id' => $id, 'status' => 0]);
        return view('exam/showExam', ['exam' => $exam[0]]);
    }

    /**
     * 检查是否能够进行考试
     *
     * @function checkPermission
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     * @author CJ
     */
    public function checkPermission(Request $request)
    {
        $id = $request->get('id');
        //获取当前用户名
        $userName = Auth::user()->name;
        //对考试id和用户名进行加密
        $encrypted = Crypt::encryptString($id . ',' . $userName);
        //生成可访问的考试链接
        $url = url('exam/startExam/' . $encrypted);
        return $url;
    }

    /**
     * 开始考试
     *
     * @function startExam
     * @param null $encrypt
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function startExam($encrypt = null)
    {
        //获取加密信息
        $encrypted = $encrypt;
        //进行解密
        try {
            $decrypted = Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            return view('errorPage', ['code' => -1, 'message' => '无效考试']);
        }
        $checkInfo = explode(',', $decrypted);
        if ($checkInfo[1] == Auth::user()->name) {
            $exam = $this->showPaper($checkInfo);
        }
        if (!$exam) {
            return view('errorPage', ['code' => -1, 'message' => '考试出现异常，请联系管理员']);
        } else {
            return view('exam/startExam', ['exam' => $exam]);
        }
    }

    /**
     * 通过考试ID获取整个考试的所有信息（多个试卷随机生成一个）
     *
     * @function showPaper
     * @param $id
     * @return \___PHPSTORM_HELPERS\static|bool|mixed
     * @author CJ
     */
    public function showPaper($id)
    {
        $exams = new Exam();
        $exam = $exams->getExam(['id' => $id, 'status' => 0]);
        if (!empty($exam)) {
            $examInfo = $exam[0];
            $papers = explode(',', $examInfo['paper_id']);
            //对试卷id进行随机抽取
            do {
                if (empty($papers)) {
                    return false;
                }
                $randId = array_rand($papers, 1);
                $paper_id = $papers[$randId];
                $paper = new Paper();
                $paperInfo = $paper->getPaper(['id' => $paper_id, 'status' => 0]);
                if (empty($paperInfo)) {
                    unset($papers[$randId]);
                }
            } while (empty($paperInfo));

            $paperInfo[0]['content'] = unserialize($paperInfo[0]['content']);

            $questionController = new QuestionController();

            foreach ($paperInfo[0]['content'] as $item => $value) {
                foreach ($value as $questionId => $score) {
                    $question = new Question();
                    $temp = [];
                    $temp = $question->getQuestionForId([$questionId]);
                    //如果试题没有找到（被删除）返回试卷不完整
                    if (empty($temp)) {
                        return false;
                    }
                    $temp = $questionController->arrangeQuestionInfo($temp);
                    $paperInfo[0]['content'][$item][$questionId] = $temp[0];
                    $paperInfo[0]['content'][$item][$questionId]['score'] = $score;
                }
            }
            $examInfo['paper_id'] = $paperInfo[0];
            return $examInfo;
        }
    }

    /**
     * 保存试卷
     *
     * @function saveExam
     * @param Request $request
     * @return array
     * @author CJ
     */
    function saveExam(Request $request)
    {
        $studentExamInfo = $request->all();
        //判断是否需要人工评卷
        $flag = false;
        //自动算分
        $paper_id = $studentExamInfo['paper_id'];
        $paper = new Paper();
        $paperInfo = $paper->getPaper(['id' => $paper_id, 'status' => 0]);
        $paperInfo[0]['content'] = unserialize($paperInfo[0]['content']);

        $questionController = new QuestionController();

        foreach ($paperInfo[0]['content'] as $item => $value) {
            foreach ($value as $questionId => $score) {
                $question = new Question();
                $temp = [];
                $temp = $question->getQuestionForId([$questionId]);
                //如果试题没有找到（被删除）返回试卷不完整
                if (empty($temp)) {
                    return false;
                }
                $temp = $questionController->arrangeQuestionInfo($temp);
                $paperInfo[0]['content'][$item][$questionId] = $temp[0];
                $paperInfo[0]['content'][$item][$questionId]['score'] = $score;
            }
        }

        $sumScore = 0;
        foreach ($paperInfo[0]['content'] as $item => $value) {
            foreach ($value as $questionId => $content) {
                if ($content['type'] == '单选题' || $content['type'] == '判断题') {
                    if (isset($studentExamInfo[$questionId]) && $studentExamInfo[$questionId] == $content['answer']) {
                        $sumScore += $content['score'];
                    }
                } else if ($content['type'] == '多选题') {
                    if (isset($studentExamInfo[$questionId])) {
                        if (array_diff($studentExamInfo[$questionId], $content['answer']) && array_diff($content['answer'], $studentExamInfo[$questionId]))
                            $sumScore += $content['score'];
                    }
                } else if ($content['type'] == '填空题') {
                    if (isset($studentExamInfo[$questionId])) {
                        if (array_diff($studentExamInfo[$questionId], $content['answer_info']) && array_diff($content['answer_info'], $studentExamInfo[$questionId]))
                            $sumScore += $content['score'];
                    }
                } else if ($content['type'] == '简答题'){
                    $flag = true;
                }
            }
        }

        //保存学生考试结果
        $answer = new UserAnswer();
        $answer->exam_id = $studentExamInfo['exam_id'];
        $answer->paper_id = $studentExamInfo['paper_id'];
        $answer->user_id = Auth::user()->id;
        unset($studentExamInfo['exam_id']);
        unset($studentExamInfo['paper_id']);
        $answer->result = json_encode($studentExamInfo);
        $answer->save();

        //保存分数
        $result = new ExamResult();
        $result->score = $sumScore;
        $result->save();

        if($answer && $result){
            if($flag){
                return [
                    'code' => 2,
                    'message' => $sumScore,
                    'url' => url('exam')
                ];
            }else{
                return [
                    'code' => 1,
                    'message' => $sumScore,
                    'url' => url('exam')
                ];
            }
        }else{
            return [
                'code' => -1,
                'message' => '考试交卷失败，请重新提交或联系管理员',
            ];
        }
    }
}
