<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Validator;
use Illuminate\Contracts\Pagination\Paginator;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use function PHPSTORM_META\type;

class QuestionController extends Controller
{
    /**
     * 中间件验证是否登录
     *
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function guest()
    {
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
    public function addQuestion($type, $id = false)
    {
        $context = [
            'status' => ['type' => $type, 'id' => $id, 'breadcrumbTop' => $id]      //类型 和 试题ID
        ];
        return view('admin/question/addQuestion', ['context' => $context]);
    }


    /**
     * 创建试题方法
     * @function createQuestion
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function createQuestion(Request $request)
    {
        //将要保存的数据
        $parameter = $request->all();
        //处理后验证的数据
        $parameters = $request->all();
        $parameters['description'] = strip_tags($parameters['description']);
        $parameters['analysis'] = strip_tags($parameters['analysis']);
        if(isset($parameters['option'])) {
            foreach ($parameters['option'] as $item => $value) {
                $parameters['option'][$item] = strip_tags($parameters['option'][$item]);
            }
        }

        //验证规则
        $vaildatedData = [];
        //自定义错误提示名称
        $vaildateName = [
            'description' => '试题描述',
            'analysis' => '试题解析',
        ];
        //错误信息自定义
        $vailErrorInfo = [
            'required' => ':attribute 必填',
            'max' => ':attribute 长度不可大于10000',
        ];

        $vaildatedData['description'] = "bail|required|max:10000";
        $vaildatedData['analysis'] = "bail|required|max:30000";


        if ($parameters['questionType'] == 'SingleChoice') {           //单选题
            //验证规则数组
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:30000";
                $vaildateName[$key] = '选项' . $item . '描述';

            }
            $vaildatedData['option_radio'] = "bail|required";
            $vaildateName['option_radio'] = '试题选项';

            //验证表单
            $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();
//            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            if (!isset($parameters['option'][$parameters['option_radio']])) {
                echo '正确答案不存在';
                return;
            }
            $type = 'radio';
        } elseif ($parameters['questionType'] == 'MultipleChoice') {       //多选题
            //验证规则数组
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:30000";
                $vaildateName[$key] = '选项' . $item . '描述';
            }
            $vaildatedData['option_checkbox'] = "bail|required";
            $vaildateName['option_checkbox'] = '试题选项';

//            dd($vaildatedData,$vailErrorInfo,$vaildateName,$parameters);


            //验证表单
            $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();
//            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);
            $type = 'checkbox';
            $parameter['option_checkbox'] = serialize($parameters['option_checkbox']);

        } elseif ($parameters['questionType'] == 'TrueOrFalse') {         //判断题
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:10000";
                $vaildateName[$key] = '选项' . $item . '描述';

            }
            $vaildatedData['option_radio'] = "bail|required";
            $vaildateName['option_radio'] = '试题选项';
            //验证表单
            $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();
//            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            if (!isset($parameters['option'][$parameters['option_radio']])) {
                echo '正确答案不存在';
                return;
            }
            $type = 'radio';
        } elseif ($parameters['questionType'] == 'FillInTheBlank') {      //填空题
            $parameters['countBlank'] = (int)$parameters['countBlank'];
//            dd($parameters);
            if(isset($parameters['option'])) {
                foreach ($parameters['option'] as $item => $value) {
                    $key = 'option.' . $item;
                    $vaildatedData[$key] = "bail|required|max:10000";
                    $vaildateName[$key] = '答案' . $item . '描述';

                }
            }
            $vaildatedData['countBlank'] = "bail|integer|min:1";
            $vaildateName['countBlank'] = "填空个数";
            $vaildatedData['option'] = "bail|required|min:1";
            $vaildateName['option'] = "填空个数";
            $vailErrorInfo['min'] = ' :attribute 必须大于一个';
            $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();
//            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);
            $parameter['option_blank'] = 'ALL';
            $type = 'blank';
        } elseif ($parameters['questionType'] == 'ShortAnswer') {         //简答题
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:10000";
                $vaildateName[$key] = '填空' . $item . '答案';

            }
            $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();
//            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            $parameter['option_short'] = 'ALL';
            $type = 'short';
        } else {                                                          //抛出异常
            return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with([
                'code' => '0',
                'message' => '未知的试题类型'
            ]);
        }
        $save = $this->saveQuestion($parameter, $type);
        if($save){
            if(empty($parameters['questionId'])){
                $message = [
                    'code' => '1',
                    'message' => '添加试题成功'
                ];
            }else{
                $message = [
                    'code' => '2',
                    'message' => '修改试题成功'
                ];
            }
            return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with($message);
        }else{
            return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with([
                'code' => '0',
                'message' => '试题保存失败'
            ]);
        }
    }

    /**
     * 保存试题到数据库 (或更新)
     *
     * @function saveQuestion
     * @param $parameters
     * @param $type
     * @return bool
     * @author CJ
     */
    public function saveQuestion($parameters, $type)
    {

        if (!empty($parameters['questionId'])) {
            $Question = Question::find($parameters['questionId']);
            if (empty($Question)) {
                $Question = new Question;
            } else {
                $Question->update_user_id = Auth::guard('admin')->user()->id;
            }

        } else {
            $Question = new Question;
            $Question->create_user_id = Auth::guard('admin')->user()->id;
            $Question->type = $parameters['questionType'];
            $Question->update_user_id = 0;
        }

        //插入试题到数据库
        $Question->description = $parameters['description'];
        $Question->analysis = $parameters['analysis'];
        $Question->answer = $parameters['option_' . $type];
        $Question->answer_info = serialize($parameters['option']);
        return $Question->save();
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
            'description' => Input::get('description', ''),
            'type' => Input::get('type', ''),
            'status' => Input::get('status', ''),
            'create_user_name' => Input::get('create_user_name', ''),
            'update_user_name' => Input::get('update_user_name', ''),
            'created_time_start' => Input::get('created_time_start', ''),
            'created_time_end' => Input::get('created_time_end', ''),
            'updated_time_start' => Input::get('updated_time_start', ''),
            'updated_time_end' => Input::get('updated_time_end', ''),
            'order_by_id' => Input::get('order_by_id', ''),
            'order_by_description' => Input::get('order_by_description', ''),
            'order_by_type' => Input::get('order_by_type', ''),
            'order_by_status' => Input::get('order_by_status', ''),
            'order_by_create_user_name' => Input::get('order_by_create_user_name', ''),
            'order_by_update_user_name' => Input::get('order_by_update_user_name', ''),
            'order_by_created_time' => Input::get('order_by_created_time', ''),
            'order_by_updated_time' => Input::get('order_by_updated_time', ''),
        ];
    }

    /**
     * 管理试题
     *
     * @function manageQuestion
     * @param bool $breadcrumbTop
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function manageQuestion($breadcrumbTop = false,Request $request)
    {
        //关联查询admins表,获取name,并进行分页
        $questions = new Question;
        $questions = $questions->pageResult(self::initParams());
        return view('admin/question/manageQuestion', [
            'questions' => $questions,
            'params' => self::initParams(),
            'context' => ['status' => ['breadcrumbTop' => $breadcrumbTop]]
        ]);
    }

    /**
     * 删除试题方法
     *
     * @function deleteQuestion
     * @return array
     * @author CJ
     */
    public function deleteQuestion()
    {
        //获取试题类型
        $deleteType = Input::get('type', '');
        //是否删除成功
        $TFSuccess = false;
        if ($deleteType == 0) {
            $questionsId = Input::get('questionsId', '');
            $questionsId = explode(',', $questionsId);
            $countQuestionsId = count($questionsId);

            $questions = new Question();
            $countDestroy = $questions::destroy($questionsId);
            //判断删除成功数量是否等于需要删除的数量
            $TFSuccess = $countDestroy == $countQuestionsId;
        } else if ($deleteType == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $questions = new Question();
            $TFSuccess = $questions->searchDelete($params);
        }
        if ($TFSuccess) {
            return [
                'code' => 0,
                'message' => '删除成功',
            ];
        } else {
            return [
                'code' => -1,
                'message' => '删除失败',
            ];
        }
    }

    /**
     * 恢复所有删除试题
     *
     * @function restoreQuestion
     * @author CJ
     */
    public function restoreQuestion()
    {
        Question::withTrashed()->restore();
        return;
    }

    /**
     * 改变试题状态
     *
     * @function statusQuestion
     * @return array
     * @author CJ
     */
    public function statusQuestion(){
        //获取试题类型
        $Type = Input::get('type', '');
        $status = Input::get('status', '');
        $statusWord = $status == 0 ? '上架' : '下架';
        //是否上下架成功
        $TFSuccess = false;
        if ($Type == 0) {
            $questionsId = Input::get('questionsId', '');
            $questionsId = explode(',', $questionsId);

            $questions = new Question();
            $TFSuccess = $questions->updateStatusQuestionForId($questionsId,$status);
        } else if ($Type == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $questions = new Question();
            $TFSuccess = $questions->updateStatusQuestionForParams($params,$status);
        }
        if ($TFSuccess) {
            return [
                'code' => 0,
                'message' => $statusWord.'成功',
            ];
        } else {
            return [
                'code' => -1,
                'message' => $statusWord.'失败',
            ];
        }
    }

    /**
     * 获取试题处理后数组
     *
     * @function getQuestionPreview
     * @param null $id
     * @return Question|array|\Illuminate\Database\Eloquent\Collection|static[]
     * @author CJ
     */
    public function getQuestionPreview($id = null){
        $questions = new Question();
        $questions = $questions->getQuestion([
            'id' => $id
        ]);

        //将试题选项排序
        foreach ($questions as $key => $value) {
            $questions[$key]['answer_info'] = unserialize($value['answer_info']);
            ksort($questions[$key]['answer_info']);
            if($questions[$key]['type'] == 'MultipleChoice'){
                $questions[$key]['answer'] = unserialize($value['answer']);
                ksort($questions[$key]['answer']);
            }
        }
        return $questions;
    }

    /**
     * 预览试题
     *
     * @function previewQuestion
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function previewQuestion($id = null)
    {
        $questions = self::getQuestionPreview($id);
        //如果没有找到返回试题不存在
        if(empty($questions)){
            return redirect('/admin/question/manageQuestion/')->with([
                'code' => '-2',
                'message' => '试题不存在'
            ]);
        }
        return view('admin/question/previewQuestion', [
            'question' => $questions,
        ]);
    }

    /**
     * 修改试题
     *
     * @function changeQuestion
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function changeQuestion($id = null)
    {
        $question = new Question();
        $question = $question->getQuestion([
            'id' => $id
        ]);
        $_old_input = $question[0];
        $_old_input['option'] = unserialize($_old_input['answer_info']);
        if($_old_input['type'] == 'MultipleChoice')
            $_old_input['option_checkbox'] = unserialize($_old_input['answer']);
        elseif ($_old_input['type'] == 'SingleChoice' || $_old_input['type'] == 'TrueOrFalse')
            $_old_input['option_radio'] = $_old_input['answer'];
        return redirect('/admin/question/addQuestion/'.$_old_input["type"].'/'.$id)->with('_old_input', $_old_input);
    }

    /**
     * 通过试题ID返回试题信息
     *
     * @function getQuestionById
     * @return string
     * @author CJ
     */
    public function getQuestionById(){
        $questionsId = Input::get('questionsId', '');
        $questionsId = explode(',', $questionsId);
        $questions = new Question();
        $questionsInfo = $questions->getQuestionForId($questionsId);
        $questionsInfo = self::arrangeQuestionInfo($questionsInfo);
        return json_encode($questionsInfo);
    }

    /**
     * 返回整理后的试题数组
     *
     * @function arrangeQuestionInfo
     * @param array $questionsInfo
     * @return array
     * @author CJ
     */
    public function arrangeQuestionInfo($questionsInfo = []){
        foreach ($questionsInfo as $item => $value){
            $questionsInfo[$item]['answer_info'] = unserialize($questionsInfo[$item]['answer_info']);
            ksort($questionsInfo[$item]['answer_info']);
            if($questionsInfo[$item]['type'] == 'MultipleChoice'){
                $questionsInfo[$item]['answer'] = unserialize($questionsInfo[$item]['answer']);
                ksort($questionsInfo[$item]['answer']);
            }
            $questionsInfo[$item]['type'] = config('exam.question_type.'.$questionsInfo[$item]['type']);
        }
        return $questionsInfo;
    }


}
