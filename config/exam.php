<?php
/**
 * Created by PhpStorm.
 * User: coloynle
 * Date: 18-2-18
 * Time: 下午9:39
 */

return [

    /*
    * 路径列表对应名称
    */
    'list_name' => [
        'admin' => '首页',

        'question' => '试题管理',
        'addQuestion' => '添加试题',
        'manageQuestion' => '管理试题',

        'paper' => '试卷管理',
        'addPaper' => '添加试卷',
        'managePaper' => '管理试卷',

        'exam' => '考试管理',
        'addExam' => '添加考试',
        'manageExam' => '管理考试',
    ],

    /*
     * 试题类型
     */
    'question_type' => [
        'SingleChoice' => '单选题',
        'MultipleChoice' => '多选题',
        'TrueOrFalse' => '判断题',
        'FillInTheBlank' => '填空题',
        'ShortAnswer' => '简答题',
    ],

    'question_status' => [
        '0' => '已发布',
        '1' => '未发布',
    ],

    'exam_type' => [
        '0' => '无需报名',
        '1' => '需要报名',
    ],

];