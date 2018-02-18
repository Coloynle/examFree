<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class QuestionController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function guest(){
        return view('admin/guest');
    }

    public function index()
    {
//        return view('admin/question/addQuestion');
    }

    public function addQuestion($type,$id = false)
    {
        $context = [
            'list' => ['question','addQuestion'],
            'status' => ['type' => $type,'id' => $id]
        ];
//        $list = ['question','addQuestion'];
//        $status = ['type' => $type,'change' => $change];
        return view('admin/question/addQuestion',['context' => $context]);
    }
}
