<?php

namespace App\Http\Controllers\Admin;

use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

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
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //类型 和 考试ID
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

        if ($request->hasFile('examImg') && $request->file('examImg')->isValid()) {
            $examImgPath = $request->file('examImg')->store('public/examImg');
            $examImgPath = substr($examImgPath,7);
            $parameter['examImg'] = $examImgPath;
        }

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
     * 保存考试到数据库 (或更新)
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

        //插入考试到数据库
        $Exam->name = $parameters['examName'];
        if(!empty($parameters['examImg'])){
            $Exam->img = $parameters['examImg'];
        }
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
            'type' => Input::get('type', ''),
            'exam_time_start' => Input::get('exam_time_start', ''),
            'exam_time_end' => Input::get('exam_time_end', ''),
            'apply_time_start' => Input::get('apply_time_start', ''),
            'apply_time_end' => Input::get('apply_time_end', ''),
            'sort' => Input::get('sort', ''),
            'status' => Input::get('status', ''),
            'create_user_name' => Input::get('create_user_name', ''),
            'update_user_name' => Input::get('update_user_name', ''),
            'created_time_start' => Input::get('created_time_start', ''),
            'created_time_end' => Input::get('created_time_end', ''),
            'updated_time_start' => Input::get('updated_time_start', ''),
            'updated_time_end' => Input::get('updated_time_end', ''),
            'order_by_id' => Input::get('order_by_id', ''),
            'order_by_name' => Input::get('order_by_name', ''),
            'order_by_type' => Input::get('order_by_type', ''),
            'order_by_exam_time_start' => Input::get('order_by_exam_time_start', ''),
            'order_by_exam_time_end' => Input::get('order_by_exam_time_end', ''),
            'order_by_apply_time_start' => Input::get('order_by_apply_time_start', ''),
            'order_by_apply_time_end' => Input::get('order_by_apply_time_end', ''),
            'order_by_sort' => Input::get('order_by_sort', ''),
            'order_by_status' => Input::get('order_by_status', ''),
            'order_by_create_user_name' => Input::get('order_by_create_user_name', ''),
            'order_by_update_user_name' => Input::get('order_by_update_user_name', ''),
            'order_by_created_time' => Input::get('order_by_created_time', ''),
            'order_by_updated_time' => Input::get('order_by_updated_time', ''),
        ];
    }

    /**
     * 管理考试
     *
     * @function managePaper
     * @param bool $breadcrumbTop
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function manageExam($breadcrumbTop = false,Request $request){
        //关联查询admins表,获取name,并进行分页
//        if($request->method() == 'POST')
//            dd($request->all());

        $exams = new Exam();
        $exams = $exams->pageResult(self::initParams());
        return view('admin/exam/manageExam', [
            'exams' => $exams,
            'params' => self::initParams(),
            'context' => ['status' => ['breadcrumbTop' => $breadcrumbTop]]
        ]);
    }

    /**
     * 修改考试
     *
     * @function changeExam
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function changeExam($id = null)
    {
        $exam = new Exam();
        //获取考试数据
        $exam = $exam->getExam([
            'id' => $id
        ]);
        $_old_input = $exam[0];
        $_old_input['examId'] = $_old_input['id'];
        $_old_input['examName'] = $_old_input['name'];
        $_old_input['examImg'] = $_old_input['img'];

        return redirect('/admin/exam/addExam/'.$id)->with('_old_input', $_old_input);
    }

    /**
     * 删除考试方法
     *
     * @function deleteExam
     * @return array
     * @author CJ
     */
    public function deleteExam()
    {
        //获取考试类型
        $deleteType = Input::get('type', '');
        //是否删除成功
        $TFSuccess = false;
        if ($deleteType == 0) {
            $examsId = Input::get('examsId', '');
            $examsId = explode(',', $examsId);
            $countexamsId = count($examsId);

            $exams = new Exam();
            $countDestroy = $exams::destroy($examsId);
            //判断删除成功数量是否等于需要删除的数量
            $TFSuccess = $countDestroy == $countexamsId;
        } else if ($deleteType == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $exams = new Exam();
            $TFSuccess = $exams->searchDelete($params);
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
     * 恢复所有删除考试
     *
     * @function restoreExam
     * @author CJ
     */
    public function restoreExam()
    {
        Exam::withTrashed()->restore();
        return;
    }

    /**
     * 改变考试状态
     *
     * @function statusExam
     * @return array
     * @author CJ
     */
    public function statusExam(){
        //获取考试类型
        $Type = Input::get('type', '');
        $status = Input::get('status', '');
        $statusWord = $status == 0 ? '上架' : '下架';
        //是否上下架成功
        $TFSuccess = false;
        if ($Type == 0) {
            $examsId = Input::get('examsId', '');
            $examsId = explode(',', $examsId);

            $exams = new Exam();
            $TFSuccess = $exams->updateStatusExamForId($examsId,$status);
        } else if ($Type == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $exams = new Exam();
            $TFSuccess = $exams->updateStatusExamForParams($params,$status);
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




}
