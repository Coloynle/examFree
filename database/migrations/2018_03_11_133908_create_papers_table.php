<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->increments('id')->comment('自增长主键');
            $table->string('name')->comment('试卷名称');
            $table->string('type')->comment('试卷分类');
            $table->integer('passing_score')->comment('及格分数');
            $table->text('content')->comment('试卷内容');
            $table->integer('create_user_id')->comment('创建者的ID');
            $table->integer('update_user_id')->default(null)->comment('修改者的ID');
            $table->integer('status')->default(0)->comment('试卷状态');
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
        Schema::dropIfExists('papers');
    }
}
