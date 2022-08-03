<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * ChangeLatlngOnRefLocationsTable Migration
 */
class ChangeLatlngOnRefLocationsTable extends Migration
{
    public function up()
    {
        Schema::table('mabaryuk_ref_locations', function($table) {
            $table->string('lat', 20)->nullable()->change();
            $table->string('lng', 20)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('mabaryuk_ref_locations', function($table) {
            $table->double('lat', 8, 2)->nullable()->change();
            $table->double('lng', 8, 2)->nullable()->change();
        });
    }
}