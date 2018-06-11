<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
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
        $select = new Exam();
        //考试ID条件
        if(!empty($params['id'])){
            $select = $select->where('id','=',$params['id']);
        }
        //考试名称
        if(!empty($params['name'])){
            $select = $select->where('name','like','%'.$params['name'].'%');
        }
        //考试类型条件
        if(isset($params['type']) && $params['type'] != ''){
            $select = $select->where('type','=',$params['type']);
        }
        //考试起始时间条件
        if(!empty($params['exam_time_start'])){
            $select = $select->where('exam_time_end','>=',$params['exam_time_start']);
        }
        //考试结束时间条件
        if(!empty($params['exam_time_end'])){
            $select = $select->where('exam_time_start','<=',$params['exam_time_end']);
        }
        //报名开始时间条件
        if(!empty($params['apply_time_start'])){
            $select = $select->where('apply_time_end','>=',$params['apply_time_start']);
        }
        //报名结束时间条件
        if(!empty($params['apply_time_end'])){
            $select = $select->where('apply_time_start','<=',$params['apply_time_end']);
        }
        //考试分类条件
        if(!empty($params['sort'])){
            $select = $select->where('sort','like','%'.$params['sort'].'%');
        }
        //考试状态条件
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
        //考试ID排序
        if(!empty($params['order_by_id'])){
            $select = $select->orderBy('id',$params['order_by_id']);
        }
        //考试类型排序
        if(!empty($params['order_by_type'])){
            $select = $select->orderBy('type',$params['order_by_type']);
        }
        //考试开始时间排序
        if(!empty($params['order_by_exam_time_start'])){
            $select = $select->orderBy('exam_time_start',$params['order_by_exam_time_start']);
        }
        //考试结束时间排序
        if(!empty($params['order_by_exam_time_end'])){
            $select = $select->orderBy('exam_time_end',$params['order_by_exam_time_end']);
        }
        //报名开始时间排序
        if(!empty($params['order_by_apply_time_start'])){
            $select = $select->orderBy('apply_time_start',$params['order_by_apply_time_start']);
        }
        //报名结束时间排序
        if(!empty($params['order_by_apply_time_end'])){
            $select = $select->orderBy('apply_time_end',$params['order_by_apply_time_end']);
        }
        //考试分类排序
        if(!empty($params['order_by_sort'])){
            $select = $select->orderBy('sort',$params['order_by_sort']);
        }
        //考试状态排序
        if(!empty($params['order_by_status'])){
            $select = $select->orderBy('status',$params['order_by_status']);
        }
        //考试创建者排序
        if(!empty($params['order_by_create_user_name'])){
            $select = $select->orderBy('create_user_id',$params['order_by_create_user_name']);
        }
        //考试修改者排序
        if(!empty($params['order_by_update_user_name'])){
            $select = $select->orderBy('update_user_id',$params['order_by_update_user_name']);
        }
        //考试创建时间排序
        if(!empty($params['order_by_created_time'])){
            $select = $select->orderBy('created_at',$params['order_by_created_time']);
        }
        //考试修改时间排序
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
    public function getExam($params = []){
        return self::searchByParams($params)->get()->toArray();
    }

    public function getExamId($params = []){
        return self::searchByParams($params)->get(['id'])->toArray();
    }

    /**
     * 通过考试ID更新考试状态
     *
     * @function updateStatusExamForId
     * @param $papersId
     * @param $status
     * @return mixed
     * @author CJ
     */
    public function updateStatusExamForId($papersId,$status){
        return self::whereIn('id',$papersId)->update(['status' => $status]);
    }


    /**
     * 通过考试检索条件更新考试状态
     *
     * @function updateStatusExamForParams
     * @param array $params
     * @param $status
     * @return bool|int
     * @author CJ
     */
    public function updateStatusExamForParams($params = [],$status){
        return self::searchByParams($params)->update(['status' => $status]);
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

    /**
     * 获得最新考试（6个）
     *
     * @function getNewestExam
     * @return array
     * @author CJ
     */
    public function getNewestExam(){
        return self::searchByParams(['status' => '0', 'order_by_updated_time' => 'desc'])->limit(6)->get()->toArray();
    }

}
