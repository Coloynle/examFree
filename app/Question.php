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
        $select = $select->where('type','=','SingleChoice');
        $select = $select->with(['getCreateUserName:id,name','getUpdateUserName:id,name'])->paginate(10);
        return $select;
    }
}
