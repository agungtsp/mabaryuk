<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateSkillLevelsTable Migration
 */
class CreateSkillLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_ref_skill_levels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->string('alias', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->tinyInteger('talent_commission')->default(0);
            $table->tinyInteger('hero_commission')->default(0);
            $table->tinyInteger('mabaryuk_commission')->default(0);

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
        Schema::dropIfExists('mabaryuk_ref_skill_levels');
    }
}
