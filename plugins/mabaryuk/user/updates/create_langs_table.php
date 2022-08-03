<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateLangsTable Migration
 */
class CreateLangsTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_user_langs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('ref_lang_id')->unsigned();
            $table->integer('ref_lang_level_id')->unsigned();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('ref_lang_id')->references('id')->on('mabaryuk_ref_langs');
            $table->foreign('ref_lang_level_id')->references('id')->on('mabaryuk_ref_lang_levels');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mabaryuk_user_langs');
    }
}
