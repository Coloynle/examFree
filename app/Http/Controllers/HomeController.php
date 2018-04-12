<?php

namespace App\Http\Controllers;

use App\Exam;
use Illuminate\Http\Request;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = new Exam();
        $newestExam = $exams->getNewestExam();
//        dd($newestExam);
        return view('home',['newestExam' => $newestExam]);
    }
}
