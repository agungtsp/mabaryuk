<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddTalentColumnsOnUsersTable Migration
 */
class AddTalentColumnsOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->boolean('is_talent')->index()->nullable();
            $table->datetime('talent_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn([
                'is_talent',
                'talent_date'
            ]);
        });
    }
}
