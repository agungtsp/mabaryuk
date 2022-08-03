<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRefIndustriesTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_ref_industries', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->string('alias', 100)->nullable();
            $table->string('description', 255)->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('created_by')->references('id')->on('backend_users');
            $table->foreign('updated_by')->references('id')->on('backend_users');
            $table->foreign('deleted_by')->references('id')->on('backend_users');
        });
    }

    public function down()
    {
        Schema::drop('mabaryuk_ref_industries');
    }
}