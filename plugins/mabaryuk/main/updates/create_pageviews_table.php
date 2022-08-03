<?php namespace MabarYuk\Main\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePageviewsTable Migration
 */
class CreatePageviewsTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_main_pageviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('view_id')->nullable();
            $table->string('view_type')->nullable();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('ip_address')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['view_id', 'view_type'], 'pageviews_master_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mabaryuk_main_pageviews');
    }
}
