<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * 获取创建者用户名
     *
     * @function getCreateUserName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */

    public function getCreateUserName(){
        return $this->belongsTo('App\Admin','create_user_id','id');
    }

    /**
     * 获取修改者用户名
     *
     * @function getCreateUserName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */
    public function getUpdateUserName(){
        return $this->belongsTo('App\Admin','update_user_id','id');
    }

    /**
     * 根据传入条件筛选结果
     *
     * @function searchByParams
     * @param array $params
     * @return Question
     * @author CJ
     */
    public function searchByParams($params = []){
        $select = new Question();
        //试题ID条件
        if(!empty($params['id'])){
            $select = $select->where('id','=',$params['id']);
        }
        //试题描述条件
        if(!empty($params['description'])){
            $select = $select->where('description','=',$params['description']);
        }
        //试题类型条件
        if(!empty($params['type'])){
            $select = $select->where('type','=',$params['type']);
        }
        //创建人条件
        if(!empty($params['create_user_name'])){
            $select = $select->where('id','=',$params['id']);
        }
        //修改人条件
        if(!empty($params['update_user_name'])){
            $select = $select->where('id','=',$params['id']);
        }
        //创建时间起始条件
        if(!empty($params['created_time_start'])){
            $select = $select->where('created_time','>=',$params['created_time_start'].' 00:00');
        }
        //创建时间结束条件
        if(!empty($params['created_time_end'])){
            $select = $select->where('created_time','<=',$params['created_time_end'].' 23:59');
        }
        //修改时间起始条件
        if(!empty($params['updated_time_start'])){
            $select = $select->where('updated_time','>=',$params['updated_time_start'].' 00:00');
        }
        //修改时间结束条件
        if(!empty($params['updated_time_end'])){
            $select = $select->where('updated_time','<=',$params['updated_time_end'].' 23:59');
        }
        $select = $select->with(['getCreateUserName:id,name','getUpdateUserName:id,name'])->paginate(10)->appends($params);
        return $select;
    }
}
