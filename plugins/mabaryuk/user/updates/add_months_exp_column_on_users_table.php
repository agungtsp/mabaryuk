<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddMonthsExpColumnOnUsersTable Migration
 */
class AddMonthsExpColumnOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->tinyInteger('months_exp')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn([
                'months_exp'
            ]);
        });
    }
}
