<?php

//use Illuminate\Support\Facades\Schema;
use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_users', function (Blueprint $table) {
            $table->increments('id')->comment('自增长主键');
            $table->string('name')->unique()->comment('用户名');
            $table->string('email')->unique()->comment('邮箱');
            $table->string('password')->comment('密码');
            $table->integer('group_id')->length(10)->nullable()->comment('组ID');
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
        Schema::dropIfExists('e_users');
    }
}
