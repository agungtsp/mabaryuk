<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateEducationTable Migration
 */
class CreateEducationTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_user_education', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('ref_education_id')->unsigned();
            $table->string('institution')->nullable();
            $table->string('major')->nullable();
            $table->char('grad_year', 4)->nullable();
            $table->decimal('gpa', 4, 2)->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('ref_education_id')->references('id')->on('mabaryuk_ref_education');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mabaryuk_user_education');
    }
}
