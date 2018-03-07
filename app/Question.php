<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    //启用软删除
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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
     * @return \Illuminate\Database\Eloquent\Builder|static
     * @author CJ
     */
    private function searchByParams($params = []){
        $select = new Question();
        //试题ID条件
        if(!empty($params['id'])){
            $select = $select->where('id','=',$params['id']);
        }
        //试题描述条件
        if(!empty($params['description'])){
            $select = $select->where('description','like','%'.$params['description'].'%');
        }
        //试题类型条件
        if(!empty($params['type'])){
            $select = $select->where('type','=',$params['type']);
        }
        //试题状态条件
        if(isset($params['status']) && $params['status'] != ''){
            $select = $select->where('status','=',(int)$params['status']);
        }
        //创建人条件
        if(!empty($params['create_user_name'])){
            $admin = new Admin();
            $admin = $admin->select('id')->where('name','=','Coloynle')->first();
            $select = $select->where('create_user_id','=',$admin->id);
        }
        //修改人条件
        if(!empty($params['update_user_name'])){
            $admin = new Admin();
            $admin = $admin->select('id')->where('name','=','Coloynle')->first();
            $select = $select->where('update_user_id','=',$admin->id);
        }
        //创建时间起始条件
        if(!empty($params['created_time_start'])){
            $select = $select->where('created_at','>=',$params['created_time_start'].' 00:00');
        }
        //创建时间结束条件
        if(!empty($params['created_time_end'])){
            $select = $select->where('created_at','<=',$params['created_time_end'].' 23:59');
        }
        //修改时间起始条件
        if(!empty($params['updated_time_start'])){
            $select = $select->where('updated_at','>=',$params['updated_time_start'].' 00:00');
        }
        //修改时间结束条件
        if(!empty($params['updated_time_end'])){
            $select = $select->where('updated_at','<=',$params['updated_time_end'].' 23:59');
        }
        //试题ID排序
        if(!empty($params['order_by_id'])){
            $select = $select->orderBy('id',$params['order_by_id']);
        }
        //试题类型排序
        if(!empty($params['order_by_type'])){
            $select = $select->orderBy('type',$params['order_by_type']);
        }
        //试题状态排序
        if(!empty($params['order_by_status'])){
            $select = $select->orderBy('status',$params['order_by_status']);
        }
        //试题创建者排序
        if(!empty($params['order_by_create_user_name'])){
            $select = $select->orderBy('create_user_id',$params['order_by_create_user_name']);
        }
        //试题修改者排序
        if(!empty($params['order_by_update_user_name'])){
            $select = $select->orderBy('update_user_id',$params['order_by_update_user_name']);
        }
        //试题创建时间排序
        if(!empty($params['order_by_created_time'])){
            $select = $select->orderBy('created_at',$params['order_by_created_time']);
        }
        //试题修改时间排序
        if(!empty($params['order_by_updated_time'])){
            $select = $select->orderBy('updated_at',$params['order_by_updated_time']);
        }
        $question = $select->with(['getCreateUserName:id,name','getUpdateUserName:id,name']);
        return $question;
    }

    /**
     * 获取分页结果
     *
     * @function pageResult
     * @param array $params
     * @return $this
     * @author CJ
     */
    public function pageResult($params = []){
//        dd(self::searchByParams($params));
        return self::searchByParams($params)->paginate(10)->appends($params);
    }

    /**
     * 通过检索条件删除试题
     *
     * @function searchDelete
     * @param array $params
     * @return bool|mixed|null
     * @author CJ
     */
    public function searchDelete($params = []){
        return self::searchByParams($params)->delete();
    }

    /**
     * 获得试题信息
     *
     * @function getQuestion
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author CJ
     */
    public function getQuestion($params = []){
        return self::searchByParams($params)->get()->toArray();
    }

    /**
     * 通过试题ID更新试题状态
     *
     * @function updateStatusQuestionForId
     * @param $questionsId
     * @param $status
     * @return mixed
     * @author CJ
     */
    public function updateStatusQuestionForId($questionsId,$status){
        return self::whereIn('id',$questionsId)->update(['status' => $status]);
    }

    /**
     * 通过试题检索条件更新试题状态
     *
     * @function updateStatusQuestionForParams
     * @param array $params
     * @param $status
     * @return bool|int
     * @author CJ
     */
    public function updateStatusQuestionForParams($params = [],$status){
        return self::searchByParams($params)->update(['status' => $status]);
    }
}
