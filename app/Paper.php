<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paper extends Model
{
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
        $select = new Paper();
        //试卷ID条件
        if(!empty($params['id'])){
            $select = $select->where('id','=',$params['id']);
        }
        //试卷名称
        if(!empty($params['name'])){
            $select = $select->where('name','like','%'.$params['name'].'%');
        }
        //试卷分类条件
        if(!empty($params['type'])){
            $select = $select->where('type','like','%'.$params['type'].'%');
        }
        //试卷状态条件
        if(isset($params['status']) && $params['status'] != ''){
            $select = $select->where('status','=',(int)$params['status']);
        }
        //创建人条件
        if (!empty($params['create_user_name'])) {
            $admin = new Admin();
            $admin = $admin->select('id')->where('name', '=', $params['create_user_name'])->first();
            if (!empty($admin))
                $select = $select->where('create_user_id', '=', $admin->id);
            else
                $select = $select->where('create_user_id', '=', -1);
        }
        //修改人条件
        if (!empty($params['update_user_name'])) {
            $admin = new Admin();
            $admin = $admin->select('id')->where('name', '=', $params['update_user_name'])->first();
            if (!empty($admin))
                $select = $select->where('update_user_id', '=', $admin->id);
            else if($params['update_user_name'] == '无')
                $select = $select->where('update_user_id', '=', 0);
            else
                $select = $select->where('update_user_id', '=', -1);
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
        //试卷ID排序
        if(!empty($params['order_by_id'])){
            $select = $select->orderBy('id',$params['order_by_id']);
        }
        //试卷分类排序
        if(!empty($params['order_by_type'])){
            $select = $select->orderBy('type',$params['order_by_type']);
        }
        //试卷状态排序
        if(!empty($params['order_by_status'])){
            $select = $select->orderBy('status',$params['order_by_status']);
        }
        //试卷创建者排序
        if(!empty($params['order_by_create_user_name'])){
            $select = $select->orderBy('create_user_id',$params['order_by_create_user_name']);
        }
        //试卷修改者排序
        if(!empty($params['order_by_update_user_name'])){
            $select = $select->orderBy('update_user_id',$params['order_by_update_user_name']);
        }
        //试卷创建时间排序
        if(!empty($params['order_by_created_time'])){
            $select = $select->orderBy('created_at',$params['order_by_created_time']);
        }
        //试卷修改时间排序
        if(!empty($params['order_by_updated_time'])){
            $select = $select->orderBy('updated_at',$params['order_by_updated_time']);
        }
        $paper = $select->with(['getCreateUserName:id,name','getUpdateUserName:id,name']);
        return $paper;
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
     * 通过检索条件删除试卷
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
     * 获得试卷信息
     *
     * @function getPaper
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author CJ
     */
    public function getPaper($params = []){
        return self::searchByParams($params)->get()->toArray();
    }

    /**
     * 通过试卷ID获取试卷信息 (状态为上架)
     *
     * @function getPaperForId
     * @param $papersId
     * @return mixed
     * @author CJ
     */
    public function getPaperForId($papersId){
        return self::whereIn('id',$papersId)->where('status','=',0)->get()->toArray();
    }

    /**
     * 通过试卷ID更新试卷状态
     *
     * @function updateStatusPaperForId
     * @param $papersId
     * @param $status
     * @return mixed
     * @author CJ
     */
    public function updateStatusPaperForId($papersId,$status){
        return self::whereIn('id',$papersId)->update(['status' => $status]);
    }


    /**
     * 通过试卷检索条件更新试卷状态
     *
     * @function updateStatusPaperForParams
     * @param array $params
     * @param $status
     * @return bool|int
     * @author CJ
     */
    public function updateStatusPaperForParams($params = [],$status){
        return self::searchByParams($params)->update(['status' => $status]);
    }
}
