<?php

namespace App\Http\Controllers\Admin;

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
        $parameters = $request->all();
//        dd($parameters);

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
        $vaildatedData['analysis'] = "bail|required|max:10000";


        if ($parameters['questionType'] == 'SingleChoice') {           //单选题
            //验证规则数组
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
        } elseif ($parameters['questionType'] == 'MultipleChoice') {       //多选题
            //验证规则数组
            foreach ($parameters['option'] as $item => $value) {
                $key = 'option.' . $item;
                $vaildatedData[$key] = "bail|required|max:10000";
                $vaildateName[$key] = '选项' . $item . '描述';
                $vaildatedData['option_checkbox.'.$item] = "bail|required";
            }
            $vaildateName['option_checkbox'] = '试题选项';

            //验证表单
            $request->validate($vaildatedData, $vailErrorInfo, $vaildateName);

            foreach ($parameters['option_checkbox'] as $item => $value) {
                if (!isset($parameters['option'][$value])) {
                    echo '正确答案不存在';
                    return;
                }
            }
            $type = 'checkbox';
            $parameters['option_checkbox'] = serialize($parameters['option_checkbox']);

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

            $parameters['option_short'] = 'ALL';
            $type = 'short';
        } else {                                                          //抛出异常
            return redirect('/admin/question/addQuestion/' . $parameters['questionType'])->with('error', '未知的试题类型');
        }
        $this->saveQuestion($parameters, $type);
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
            if(empty($Question)){
                $Question = new Question;
            }else{
                $Question->update_user_id = Auth::guard('admin')->user()->id;
            }
        }else{
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


    public function manageQuestion(Request $request){
        $params = [
            'id' =>Input::get('id',''),
            'description' =>Input::get('description',''),
            'type' =>Input::get('type',''),
            'create_user_name' =>Input::get('create_user_name',''),
            'update_user_name' =>Input::get('update_user_name',''),
            'created_time_start' =>Input::get('created_time_start',''),
            'created_time_end' =>Input::get('created_time_end',''),
            'updated_time_start' =>Input::get('updated_time_start',''),
            'updated_time_end' =>Input::get('updated_time_end',''),
            'order_by_id' =>Input::get('order_by_id',''),
            'order_by_description' =>Input::get('order_by_description',''),
            'order_by_type' =>Input::get('order_by_type',''),
            'order_by_create_user_name' =>Input::get('order_by_create_user_name',''),
            'order_by_update_user_name' =>Input::get('order_by_update_user_name',''),
            'order_by_created_time' =>Input::get('order_by_created_time',''),
            'order_by_updated_time' =>Input::get('order_by_updated_time',''),
        ];

        $context = [
            ];
        //关联查询admins表,获取name,并进行分页
//        $questions = Question::with(['getCreateUserName:id,name','getUpdateUserName:id,name'])->paginate(1);
        $questions = new Question;
        $questions = $questions->searchByParams([
            'id' => $params['id'],
            'description' => $params['description'],
            'type' => $params['type'],
            'create_user_name' => $params['create_user_name'],
            'update_user_name' => $params['update_user_name'],
            'created_time_start' => $params['created_time_start'],
            'created_time_end' => $params['created_time_end'],
            'updated_time_start' => $params['updated_time_start'],
            'updated_time_end' => $params['updated_time_end'],
            'order_by_id' => $params['order_by_id'],
            'order_by_description' => $params['order_by_description'],
            'order_by_create_user_name' => $params['order_by_create_user_name'],
            'order_by_update_user_name' => $params['order_by_update_user_name'],
            'order_by_created_time' => $params['order_by_created_time'],
            'order_by_updated_time' => $params['order_by_updated_time'],
        ]);
        dd($questions);
        return view('admin/question/manageQuestion',[
//            'context' => $context,
            'questions' => $questions,
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
