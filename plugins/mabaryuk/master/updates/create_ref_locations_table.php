<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRefLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_ref_locations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code', 20)->index();
            $table->string('parent_code', 20)->nullable()->index();
            $table->string('name');
            $table->string('alias')->nullable();
            $table->string('postal_code',10)->nullable();
            $table->double('lat', 8, 2)->nullable();
            $table->double('lng', 8, 2)->nullable();

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
        Schema::drop('mabaryuk_ref_locations');
    }
}