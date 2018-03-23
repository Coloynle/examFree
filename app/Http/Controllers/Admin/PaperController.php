<?php

namespace App\Http\Controllers\Admin;

use App\Paper;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PaperController extends Controller
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
     * 添加试卷界面
     *
     * @function addPaper
     * @param bool $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function addPaper($id = false){
        $context = [
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //类型 和 试卷ID
        ];
        return view('admin/paper/addPaper',['context' => $context]);
    }

    /**
     * 保存试卷
     *
     * @function savePaper
     * @return array
     * @author CJ
     */
    public function savePaper()
    {
        $parameters = Input::get();
        if (!empty($parameters['paperId'])) {
            $Paper = Paper::find($parameters['paperId']);
            if (empty($Paper)) {
                $Paper = new Paper;
            } else {
                $Paper->update_user_id = Auth::guard('admin')->user()->id;
            }
        } else {
            $Paper = new Paper;
            $Paper->create_user_id = Auth::guard('admin')->user()->id;
            $Paper->update_user_id = 0;
        }

        //插入试卷到数据库
        $Paper->name = $parameters['name'];
        $Paper->type = $parameters['type'];
        $Paper->total_score = $parameters['total_score'];
        $Paper->passing_score = $parameters['passing_score'];
        $Paper->content = serialize($parameters['content']);
        $save = $Paper->save();
        if($save){
            if(empty($parameters['paperId'])){
                $message = [
                    'code' => '1',
                    'message' => '添加试卷成功'
                ];
            }else{
                $message = [
                    'code' => '2',
                    'message' => '修改试卷成功'
                ];
            }
        }else{
            $message = [
                'code' => '0',
                'message' => '试卷保存失败'
            ];
        }
        return $message;
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
            'order_by_status' => Input::get('order_by_status', ''),
            'order_by_create_user_name' => Input::get('order_by_create_user_name', ''),
            'order_by_update_user_name' => Input::get('order_by_update_user_name', ''),
            'order_by_created_time' => Input::get('order_by_created_time', ''),
            'order_by_updated_time' => Input::get('order_by_updated_time', ''),
        ];
    }

    /**
     * 管理试卷
     *
     * @function managePaper
     * @param bool $breadcrumbTop
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function managePaper($breadcrumbTop = false,Request $request){
        //关联查询admins表,获取name,并进行分页
        $papers = new Paper();
        $papers = $papers->pageResult(self::initParams());
        return view('admin/paper/managePaper', [
            'papers' => $papers,
            'params' => self::initParams(),
            'context' => ['status' => ['breadcrumbTop' => $breadcrumbTop]]
        ]);
    }

    /**
     * 删除试卷方法
     *
     * @function deletePaper
     * @return array
     * @author CJ
     */
    public function deletePaper()
    {
        //获取试卷类型
        $deleteType = Input::get('type', '');
        //是否删除成功
        $TFSuccess = false;
        if ($deleteType == 0) {
            $papersId = Input::get('papersId', '');
            $papersId = explode(',', $papersId);
            $countpapersId = count($papersId);

            $papers = new Paper();
            $countDestroy = $papers::destroy($papersId);
            //判断删除成功数量是否等于需要删除的数量
            $TFSuccess = $countDestroy == $countpapersId;
        } else if ($deleteType == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $papers = new Paper();
            $TFSuccess = $papers->searchDelete($params);
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
     * 恢复所有删除试卷
     *
     * @function restorePaper
     * @author CJ
     */
    public function restorePaper()
    {
        Paper::withTrashed()->restore();
        return;
    }

    /**
     * 改变试卷状态
     *
     * @function statusPaper
     * @return array
     * @author CJ
     */
    public function statusPaper(){
        //获取试卷类型
        $Type = Input::get('type', '');
        $status = Input::get('status', '');
        $statusWord = $status == 0 ? '上架' : '下架';
        //是否上下架成功
        $TFSuccess = false;
        if ($Type == 0) {
            $papersId = Input::get('papersId', '');
            $papersId = explode(',', $papersId);

            $papers = new Paper();
            $TFSuccess = $papers->updateStatusPaperForId($papersId,$status);
        } else if ($Type == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $papers = new Paper();
            $TFSuccess = $papers->updateStatusPaperForParams($params,$status);
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
     * 预览试卷
     *
     * @function previewPaper
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function previewPaper($id = null)
    {
        $questionController = new QuestionController();
        $papers = new Paper();
        $papers = $papers->getPaper([
            'id' => $id
        ]);

        //如果没有找到返回试卷不存在
        if(empty($papers)){
            return redirect('/admin/paper/managePaper/')->with([
                'code' => '-2',
                'message' => '试卷不存在'
            ]);
        }

        $papers[0]['content'] = unserialize($papers[0]['content']);
//        dd($papers[0]['content']);
        foreach ($papers[0]['content'] as $item => $value){
            foreach ($value as $questionId => $score){
                $question = new Question();
                $temp = [];
                $temp = $question->getQuestionForId([$questionId]);
                //如果试题没有找到（被删除）返回试卷不完整
                if(empty($temp)){
                    return redirect('/admin/paper/managePaper/')->with([
                        'code' => '-2',
                        'message' => '试卷不完整(试题ID'.$questionId.'被下架或删除)'
                    ]);
                }
                $temp = $questionController->arrangeQuestionInfo($temp);
                $papers[0]['content'][$item][$questionId] = $temp[0];
                $papers[0]['content'][$item][$questionId]['score'] = $score;
            }
        }

//        dd($papers[0]);
        return view('admin/paper/previewPaper', [
            'paper' => $papers[0],
        ]);
    }

    /**
     * 修改试卷
     *
     * @function changePaper
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function changePaper($id = null)
    {
        $questionController = new QuestionController();
        $paper = new Paper();
        //获取试卷数据
        $paper = $paper->getPaper([
            'id' => $id
        ]);
        $_old_input = $paper[0];
        $_old_input['content'] = unserialize($_old_input['content']);
        foreach ($_old_input['content'] as $item => $value){
            foreach ($_old_input['content'][$item] as $questionId => $score){
                $question = new Question();
                $temp = [];
                $temp = $question->getQuestionForId([$questionId]);
                $temp = $questionController->arrangeQuestionInfo($temp);
                $_old_input['content'][$item][$questionId] = [];
                $_old_input['content'][$item][$questionId]['score'] = $score;
                $_old_input['content'][$item][$questionId]['type'] = $temp[0]['type'];
                $_old_input['content'][$item][$questionId]['description'] = $temp[0]['description'];
            }
        }
        return redirect('/admin/paper/addPaper/'.$id)->with('_old_input', $_old_input);
    }

    /**
     * 通过试卷ID返回试卷信息
     *
     * @function getPaperById
     * @return string
     * @author CJ
     */
    public function getPaperById(){
        $papersId = Input::get('papersId', '');
        $papersId = explode(',', $papersId);
        $papers = new Paper();
        $papersInfo = $papers->getPaperForId($papersId);
        $papersInfo = self::arrangePaperInfo($papersInfo);
        return json_encode($papersInfo);
    }

    /**
     * 返回整理后的试卷数组
     *
     * @function arrangePaperInfo
     * @param array $papersInfo
     * @return array
     * @author CJ
     */
    private function arrangePaperInfo($papersInfo = []){
        foreach ($papersInfo as $item => $value){
            $papersInfo[$item]['answer_info'] = unserialize($papersInfo[$item]['answer_info']);
            if($papersInfo[$item]['type'] == 'MultipleChoice'){
                $papersInfo[$item]['answer'] = unserialize($papersInfo[$item]['answer']);
            }
            $papersInfo[$item]['type'] = config('exam.paper_type.'.$papersInfo[$item]['type']);
        }
        return $papersInfo;
    }
}
