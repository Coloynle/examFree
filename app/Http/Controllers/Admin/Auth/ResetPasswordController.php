<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword()
    {
        return view('admin/auth/reset');
    }

    public function changePassword(Request $request){
        //将要保存的数据
        $parameter = $request->all();
        //作为验证的数据
        $parameters = $request->all();
        //验证规则
        $vaildatedData = [];
        //自定义错误提示名称
        $vaildateName = [
            'password' => '密码',
        ];
        //错误信息自定义
        $vailErrorInfo = [
            'required' => ':attribute 必填',
            'unique' => ':attribute 已存在',
            'password.confirmed'=>'两次密码不一致！',
            'password.min'=>'密码至少6位数！',
        ];

        $vaildatedData['password'] = "bail|required|confirmed|min:6";

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
            return redirect('admin/index')->with($message);
        }else{
            return redirect('admin/index')->with([
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
        $user = Admin::find($parameters['userId']);
        $user->password = Hash::make($parameters['password']);
        return $user->save();
    }
}
