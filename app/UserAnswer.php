<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnswer extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * 需要属性转换的字段
     *
     * @var array
     */
    protected $casts = [
        'result' => 'array'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];


    /**
     * 获取考试用户信息
     *
     * @function getExamUser
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */
    public function getExamUser(){
        return $this->belongsTo('App\User','user_id','id');
    }

    /**
     * 获取改卷用户信息
     *
     * @function getMEUser
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */
    public function getMEUser(){
        return $this->belongsTo('App\Admin','manual_evaluation_user_id','id');
    }

    /**
     * 获取考试信息
     *
     * @function getExam
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */
    public function getExam(){
        return $this->belongsTo('App\Exam','exam_id','id');
    }

    /**
     * 获取试卷信息
     *
     * @function getPaper
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author CJ
     */
    public function getPaper(){
        return $this->belongsTo('App\Exam','paper_id','id');
    }


    /**
     * 根据传入条件筛选结果
     *
     * @function searchByParamsForExamPreview
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     * @author CJ
     */
    private function searchByParamsForExamPreview($params = []){
        $select = new UserAnswer();

        $exam = new Exam();
        $exam = $exam->getExamId($params);
        $exam_id = [];
        foreach ($exam as $item => $value){
            $exam_id[$item] = $value['id'];
        }
        $select->whereIn('exam_id',$exam_id);
        //创建时间起始条件
        if(!empty($params['created_time_start'])){
            $select = $select->where('created_at','>=',$params['created_time_start'].' 00:00');
        }
        //创建时间结束条件
        if(!empty($params['created_time_end'])){
            $select = $select->where('created_at','<=',$params['created_time_end'].' 23:59');
        }
        //交卷时间排序
        if(!empty($params['order_by_created_time'])){
            $select = $select->orderBy('created_at',$params['order_by_created_time']);
        }
        $select = $select->where('manual_evaluation','=',true);
        $select = $select->orderBy('created_at','desc')->groupBy('exam_id');
        $examPreview = $select->with(['getExam','getExamUser:id,name']);
        return $examPreview;
    }

    /**
     * 获取第一级考试结果分页
     *
     * @function getExamPaginate
     * @param array $params
     * @param int $perPage
     * @return $this
     * @author CJ
     */
    public function getExamPaginate($params = [],$perPage = 10){
        return self::searchByParamsForExamPreview($params)->paginate($perPage)->appends($params);
    }


}
