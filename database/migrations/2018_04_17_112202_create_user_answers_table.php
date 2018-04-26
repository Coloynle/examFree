<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->increments('id')->comment('自增长主键');
            $table->integer('exam_id')->comment('考试ID');
            $table->integer('paper_id')->comment('试卷ID');
            $table->integer('user_id')->comment('用户ID');
            $table->json('result')->comment('用户所提交答案');
            $table->boolean('manual_evaluation')->comment('是否需要人工评卷');
            $table->integer('manual_evaluation_user_id')->comment('批卷人ID');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_answers');
    }
}
