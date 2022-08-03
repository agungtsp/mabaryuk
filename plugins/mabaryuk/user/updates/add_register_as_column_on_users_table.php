<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddRegisterAsColumnOnUsersTable Migration
 */
class AddRegisterAsColumnOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->enum('register_as', ['talent', 'hero'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn([
                'register_as'
            ]);
        });
    }
}
