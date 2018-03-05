<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id')->comment('自增长主键');
            $table->longText('description')->comment('试题描述');
            $table->string('type')->comment('试题类型（单选多选填空判断简答）');
            $table->longText('answer_info')->comment('答案描述');
            $table->string('answer',450)->comment('试题答案');
            $table->longText('analysis')->comment('试题详解');
            $table->integer('create_user_id')->comment('创建者的ID');
            $table->integer('update_user_id')->default(null)->comment('修改者的ID');
            $table->integer('status')->default(0)->comment('试题状态');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
