<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddDepositColumnsOnSkillLevelTable Migration
 */
class AddDepositColumnsOnSkillLevelTable extends Migration
{
    public function up()
    {
        Schema::table('mabaryuk_ref_skill_levels', function($table)
        {
            $table->decimal('min_fee', 15, 2)->nullable();
            $table->tinyInteger('min_year_exp')->nullable();
            $table->tinyInteger('max_year_exp')->nullable();
        });
    }

    public function down()
    {
        Schema::table('mabaryuk_ref_skill_levels', function($table) {
            $table->dropColumn([
                'min_fee',
                'min_year_exp',
                'max_year_exp',
            ]);
        });
    }
}
