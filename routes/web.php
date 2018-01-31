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