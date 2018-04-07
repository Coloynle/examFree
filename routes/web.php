<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 *  根目录welcome页
 */
Route::get('/', function () {
    return view('welcome');
});
/**
 *  前台登录路由
 */
Auth::routes();

/**
 *  前台Home
 */
Route::get('/home', 'HomeController@index')->name('home');

/**
 *  后台登录
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Admin\IndexController@guest');
    Route::get('login', 'Admin\Auth\LoginController@showLoginForm');
    Route::post('login', 'Admin\Auth\LoginController@login');
    Route::post('logout', 'Admin\Auth\LoginController@logout');
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/index', 'Admin\IndexController@index');
    });
});

/**
 *  试题管理
 */
Route::group(['prefix' => 'admin/question'], function () {
    Route::get('addQuestion/{type}/{id?}', 'Admin\QuestionController@addQuestion'); //添加试题
    Route::post('createQuestion/', 'Admin\QuestionController@createQuestion');      //创建试题方法
    Route::get('manageQuestion/{breadcrumbTop?}', 'Admin\QuestionController@manageQuestion');       //管理试题初始
    Route::post('manageQuestion/', 'Admin\QuestionController@manageQuestion');      //管理试题条件搜索
    Route::get('changeQuestion/{id}', 'Admin\QuestionController@changeQuestion');   //修改试题
    Route::post('deleteQuestion/{id?}', 'Admin\QuestionController@deleteQuestion'); //删除试题
    Route::post('statusQuestion/{id?}', 'Admin\QuestionController@statusQuestion'); //上下架试题
    Route::get('previewQuestion/{id?}', 'Admin\QuestionController@previewQuestion'); //预览试题
    Route::post('getQuestionById/', 'Admin\QuestionController@getQuestionById'); //通过试题ID获取试题信息JSON数组（AJAX）
});

/**
 * 试卷管理
 */
Route::group(['prefix' => 'admin/paper'], function () {
    Route::get('addPaper/{id?}', 'Admin\PaperController@addPaper');   //添加试卷
    Route::post('savePaper/', 'Admin\PaperController@savePaper');   //保存试卷
    Route::get('managePaper/{breadcrumbTop?}', 'Admin\PaperController@managePaper');   //管理试卷
    Route::post('managePaper/', 'Admin\PaperController@managePaper');   //管理试卷条件搜索
    Route::get('changePaper/{id}', 'Admin\PaperController@changePaper');   //修改试卷
    Route::post('deletePaper/{id?}', 'Admin\PaperController@deletePaper'); //删除试卷
    Route::post('statusPaper/{id?}', 'Admin\PaperController@statusPaper'); //上下架试卷
    Route::get('previewPaper/{id?}', 'Admin\PaperController@previewPaper'); //预览试卷
    Route::post('getPaperById/', 'Admin\PaperController@getPaperById'); //通过试卷ID获取试卷信息JSON数组（AJAX）
});

/**
 * 考试管理
 */
Route::group(['prefix' => 'admin/exam'], function () {
    Route::get('addExam/{id?}', 'Admin\ExamController@addExam');  //添加考试
    Route::post('createExam/', 'Admin\ExamController@createExam');   //创建考试方法
    Route::get('manageExam/{breadcrumbTop?}', 'Admin\ExamController@manageExam');   //管理考试
    Route::post('manageExam/', 'Admin\ExamController@manageExam');   //管理考试
    Route::get('changeExam/{id}', 'Admin\ExamController@changeExam');   //修改考试
    Route::post('deleteExam/{id?}', 'Admin\ExamController@deleteExam'); //删除考试
    Route::post('statusExam/{id?}', 'Admin\ExamController@statusExam'); //上下架考试
    Route::get('restoreExam/', 'Admin\ExamController@restoreExam'); //恢复软删除的考试
});