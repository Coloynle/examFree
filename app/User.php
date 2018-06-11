<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    private function searchByParams($params = []){
        $select = new User();
        //用户ID条件
        if(!empty($params['id'])){
            $select = $select->where('id','=',$params['id']);
        }
        //用户名称
        if(!empty($params['name'])){
            $select = $select->where('name','like','%'.$params['name'].'%');
        }
        //用户email
        if(!empty($params['email'])){
            $select = $select->where('email','like','%'.$params['email'].'%');
        }
        //用户ID排序
        if(!empty($params['order_by_id'])){
            $select = $select->orderBy('id',$params['order_by_id']);
        }
        //用户名排序
        if(!empty($params['order_by_name'])){
            $select = $select->orderBy('name',$params['order_by_name']);
        }
        //email排序
        if(!empty($params['order_by_email'])){
            $select = $select->orderBy('email',$params['order_by_email']);
        }
        return $select;
    }

    /**
     * 获取分页结果
     *
     * @function pageResult
     * @param array $params
     * @param int $perPage
     * @return $this
     * @author CJ
     */
    public function pageResult($params = [],$perPage = 10){
        return self::searchByParams($params)->paginate($perPage)->appends($params);
    }

    /**
     * 获得考试信息
     *
     * @function getExam
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author CJ
     */
    public function getUser($params = []){
        return self::searchByParams($params)->get()->toArray();
    }

    /**
     * 通过检索条件删除考试
     *
     * @function searchDelete
     * @param array $params
     * @return bool|mixed|null
     * @throws \Exception
     * @author CJ
     */
    public function searchDelete($params = []){
        return self::searchByParams($params)->delete();
    }
}
