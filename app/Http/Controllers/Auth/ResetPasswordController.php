<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest');
    }

    public function resetPassword()
    {
        return view('auth/passwords/reset');
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
            return redirect('home')->with($message);
        }else{
            return redirect('home')->with([
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
        $user = User::find($parameters['userId']);
        $user->password = Hash::make($parameters['password']);
        return $user->save();
    }
}
