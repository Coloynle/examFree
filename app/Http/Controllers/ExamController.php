<?php

namespace App\Http\Controllers;

use App\Exam;
use Illuminate\Http\Request;

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

    public function index(){
        $exams = new Exam();
        $exam = $exams->pageResult(['status' => '0'],9);
        return view('exam/index',['exams' => $exam]);
    }

    public function showExam($id = null){
        $exams = new Exam();
        $exam = $exams->getExam(['id' => $id]);
        return view('exam/showExam',['exam' => $exam[0]]);
    }
}
