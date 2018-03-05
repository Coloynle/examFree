<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Contracts\Pagination\Paginator;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use function PHPSTORM_META\type;

class QuestionController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
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
            'status' => ['type' => $type, 'id' => $id]      //类型 和 试题ID
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
        $parameter = $request->all();
        $parameters = $request->all();
        $parameters['description'] = strip_tags($parameters['description']);
        $parameters['analysis'] = strip_tags($parameters['description']);
        foreach ($parameters['option'] as $item =>$value){
            $parameters['option'][$item] = strip_tags($parameters['option'][$item]);
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

        $vaildatedData['description'] = "bail|required|max:30000";
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
            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

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
            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

//            foreach ($parameters['option_checkbox'] as $item => $value) {
//                if (!isset($parameters['option'][$value])) {
//                    echo '正确答案不存在';
//                    return;
//                }
//            }
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
            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            if (!isset($parameters['option'][$parameters['option_radio']])) {
                echo '正确答案不存在';
                return;
            }
            $type = 'radio';
        } elseif ($parameters['questionType'] == 'FillInTheBlank') {      //填空题

        } elseif ($parameters['questionType'] == 'ShortAnswer') {         //简答题
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:10000";
                $vaildateName[$key] = '答案' . $item . '描述';

            }
            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            $parameter['option_short'] = 'ALL';
            $type = 'short';
        } else {                                                          //抛出异常
            return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with('error', '未知的试题类型');
        }

        $this->saveQuestion($parameter, $type);
        return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with('success', '添加试题成功');
    }

    /**
     * 保存试题到数据库 (或更新)
     * @function saveQuestion
     * @param $parameters
     * @param $type
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
        $Question->save();
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
            'create_user_name' => Input::get('create_user_name', ''),
            'update_user_name' => Input::get('update_user_name', ''),
            'created_time_start' => Input::get('created_time_start', ''),
            'created_time_end' => Input::get('created_time_end', ''),
            'updated_time_start' => Input::get('updated_time_start', ''),
            'updated_time_end' => Input::get('updated_time_end', ''),
            'order_by_id' => Input::get('order_by_id', ''),
            'order_by_description' => Input::get('order_by_description', ''),
            'order_by_type' => Input::get('order_by_type', ''),
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function manageQuestion(Request $request)
    {
        //关联查询admins表,获取name,并进行分页
        $questions = new Question;
        $questions = $questions->pageResult(self::initParams());
        return view('admin/question/manageQuestion', [
            'questions' => $questions,
            'params' => self::initParams()
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
     * 预览试题
     *
     * @function previewQuestion
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function previewQuestion($id = null)
    {
        $questions = new Question();
        $questions = $questions->getQuestion([
            'id' => $id
        ]);
        foreach ($questions as $key => $value) {
            $questions[$key]['answer_info'] = unserialize($value['answer_info']);
            ksort($questions[$key]['answer_info']);
            if($questions[$key]['type'] == 'MultipleChoice'){
                $questions[$key]['answer'] = unserialize($value['answer']);
                ksort($questions[$key]['answer']);
            }
        }
//        dd($questions);

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
     * @function changeQuestion
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function changeQuestion()
    {
        $admin = new Admin();
        $admin = $admin->select('id')->where('name', '=', 'Coloynle')->first();
        dd($admin->id);
        exit;
        $_old_input = [
            'description' => 'asd',
//            'analysis' => 'asd',
            'option' => [
                'A' => '1',
                'B' => '2',
                'C' => '3',
//                'D' => '4',
            ],
        ];
        return redirect('/admin/question/addQuestion/SingleChoice/1')->with('_old_input', $_old_input);
    }


}
