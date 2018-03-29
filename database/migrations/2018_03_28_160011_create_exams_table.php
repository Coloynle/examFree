<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id')->comment('自增长主键');
            $table->string('name')->comment('考试名称');
            $table->integer('type')->comment('考试类型');
            $table->string('sort')->comment('考试分类');
            $table->timestamp('exam_time_start')->nullable()->comment('考试开始时间');
            $table->timestamp('exam_time_end')->nullable()->comment('考试结束时间');
            $table->timestamp('apply_time_start')->nullable()->comment('报名开始时间');
            $table->timestamp('apply_time_end')->nullable()->comment('报名结束时间');
            $table->text('description')->comment('考试描述');
            $table->string('paper_id')->comment('试卷ID');
            $table->integer('create_user_id')->comment('创建者的ID');
            $table->integer('update_user_id')->nullable()->comment('修改者的ID');
            $table->integer('status')->default(0)->comment('考试状态');
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
        Schema::dropIfExists('exams');
    }
}
