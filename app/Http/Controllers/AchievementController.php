<?php

namespace App\Http\Controllers;

use App\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
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
        $userAnswer = new UserAnswer();
        $achievement = $userAnswer->getDetailsPaginate(['user_id' => Auth::user()->id]);
//        dd($achievement);
        return view('achievement/index',['achievements' => $achievement]);
    }
}
