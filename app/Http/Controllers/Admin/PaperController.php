<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function addPaper($id = false){
        $context = [
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //类型 和 试题ID
        ];
        return view('admin/paper/addPaper',['context' => $context]);
    }
}
