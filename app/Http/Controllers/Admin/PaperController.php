<?php

namespace App\Http\Controllers\Admin;

use App\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PaperController extends Controller
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
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //类型 和 试题ID
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
            $Question = Paper::find($parameters['paperId']);
            if (empty($Question)) {
                $Question = new Paper;
            } else {
                $Question->update_user_id = Auth::guard('admin')->user()->id;
            }
        } else {
            $Question = new Paper;
            $Question->create_user_id = Auth::guard('admin')->user()->id;
            $Question->update_user_id = 0;
        }

        //插入试题到数据库
        $Question->name = $parameters['name'];
        $Question->type = $parameters['type'];
        $Question->total_score = $parameters['total_score'];
        $Question->passing_score = $parameters['passing_score'];
        $Question->content = serialize($parameters['content']);
        $save = $Question->save();
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
        }else{
            $message = [
                'code' => '0',
                'message' => '试题保存失败'
            ];
        }
        return $message;
    }
}
