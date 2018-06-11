<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
            'email' => Input::get('email', ''),
            'password' => Input::get('password',''),
            'order_by_id' => Input::get('order_by_id', ''),
            'order_by_name' => Input::get('order_by_name', ''),
            'order_by_email' => Input::get('order_by_email', ''),
        ];
    }

    /**
     * 添加用户界面
     *
     * @function addUser
     * @param bool $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function addUser($id = false){
        $context = [
            'status' => ['id' => $id, 'breadcrumbTop' => $id]      //用户ID 和 类型
        ];
        return view('admin/user/addUser',['context' => $context]);
    }

    /**
     * 管理用户
     *
     * @function managePaper
     * @param bool $breadcrumbTop
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author CJ
     */
    public function manageUser($breadcrumbTop = false,Request $request){
        //关联查询admins表,获取name,并进行分页
//        if($request->method() == 'POST')
//            dd($request->all());

        $users = new User();
        $users = $users->pageResult(self::initParams());

//        dd($users);
        return view('admin/user/manageUser', [
            'users' => $users,
            'params' => self::initParams(),
            'context' => ['status' => ['breadcrumbTop' => $breadcrumbTop]]
        ]);
    }

    /**
     * 修改用户
     *
     * @function changeUser
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     * @author CJ
     */
    public function changeUser($id = null)
    {
        $user = new User();
        //获取用户数据
        $user = $user->getUser([
            'id' => $id
        ]);

        $_old_input = $user[0];
        $_old_input['userId'] = $_old_input['id'];
        $_old_input['userName'] = $_old_input['name'];
        $_old_input['userEmail'] = $_old_input['email'];
        $_old_input['userPassword'] = $_old_input['password'];

        return redirect('/admin/user/addUser/'.$id)->with('_old_input', $_old_input);
    }

    /**
     * 删除用户方法
     *
     * @function deleteUser
     * @return array
     * @throws \Exception
     * @author CJ
     */
    public function deleteUser()
    {
        //获取用户类型
        $deleteType = Input::get('type', '');
        //是否删除成功
        $TFSuccess = false;
        if ($deleteType == 0) {
            $usersId = Input::get('usersId', '');
            $usersId = explode(',', $usersId);
            $countusersId = count($usersId);
            $users = new User();
            $countDestroy = $users::destroy($usersId);
            //判断删除成功数量是否等于需要删除的数量
            $TFSuccess = $countDestroy == $countusersId;
        } else if ($deleteType == 1) {
            $params = Input::get('params');
            $params = unserialize($params);
            $users = new User();
            $TFSuccess = $users->searchDelete($params);
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

    public function createUser(Request $request){
        //将要保存的数据
        $parameter = $request->all();
        //作为验证的数据
        $parameters = $request->all();
        //验证规则
        $vaildatedData = [];
        //自定义错误提示名称
        $vaildateName = [
            'userName' => '用户名',
            'userEmail' => '邮箱',
            'userPassword' => '密码',
        ];
        //错误信息自定义
        $vailErrorInfo = [
            'required' => ':attribute 必填',
            'max' => ':attribute 长度不可大于10',
            'email' => ':attribute 必须为格式正确的电子邮件地址',
            'unique' => ':attribute 已存在'
        ];

        $vaildatedData['userName'] = ["bail","required","max:10",Rule::unique('users','name')->ignore($parameter['userId'])];
        $vaildatedData['userEmail'] = ["bail","required","email",Rule::unique('users','email')->ignore($parameter['userId'])];
        $vaildatedData['userPassword'] = "bail|required";

        $validator = \Validator::make($parameters,$vaildatedData,$vailErrorInfo,$vaildateName)->validate();

        $save = self::saveUser($parameter);
        if($save){
            if(empty($parameters['userId'])){
                $message = [
                    'code' => '1',
                    'message' => '添加用户成功'
                ];
            }else{
                $message = [
                    'code' => '2',
                    'message' => '修改用户成功'
                ];
            }
            return redirect('/admin/user/addUser/')->with($message);
        }else{
            return redirect('/admin/user/addUser/')->with([
                'code' => '0',
                'message' => '用户保存失败'
            ]);
        }
    }

    /**
     * 保存用户到数据库 (或更新)
     *
     * @function saveUser
     * @param $parameters
     * @return bool
     * @author CJ
     */
    public function saveUser($parameters)
    {
        if (!empty($parameters['userId'])) {
            $user = User::find($parameters['userId']);
            if($user['password'] != $parameters['userPassword']){
                $user->password = Hash::make($parameters['userPassword']);
            }
            if (empty($user)) {
                $user = new User;
            }
        } else {
            $user = new User;
            $user->password = Hash::make($parameters['userPassword']);
        }

        //插入用户到数据库
        $user->name = $parameters['userName'];
        $user->email = $parameters['userEmail'];
        return $user->save();
    }

    /**
     * 恢复所有删除考试
     *
     * @function restoreUser
     * @author CJ
     */
    public function restoreUser()
    {
        User::withTrashed()->restore();
        return;
    }

}
