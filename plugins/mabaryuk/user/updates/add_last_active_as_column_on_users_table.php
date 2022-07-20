<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddLastActiveAsColumnOnUsersTable Migration
 */
class AddLastActiveAsColumnOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('last_active_as', 30)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn([
                'last_active_as'
            ]);
        });
    }
}
