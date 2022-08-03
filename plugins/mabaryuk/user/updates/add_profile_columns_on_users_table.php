<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddProfileColumnsOnUsersTable Migration
 */
class AddProfileColumnsOnUsersTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumns('users', [
            'phone',
            'gender',
            'address',
            'province_code',
            'city_code',
            'district_code',
            'village_code',
            'ref_country_id'
        ])) {
            return;
        }

        Schema::table('users', function($table)
        {
            $table->string('phone', 30)->nullable();
            $table->enum('gender', ['F', 'M'])->nullable();
            $table->string('address')->nullable();
            $table->string('province_code', 20)->index()->nullable();
            $table->string('city_code', 20)->index()->nullable();
            $table->string('district_code', 20)->index()->nullable();
            $table->string('village_code', 20)->index()->nullable();
            $table->integer('ref_country_id')->unsigned()->nullable();

            $table->foreign('ref_country_id')->references('id')->on('mabaryuk_ref_countries');
            $table->foreign('province_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('city_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('district_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('village_code')->references('code')->on('mabaryuk_ref_locations');
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropForeign(['ref_country_id']);
            $table->dropForeign(['province_code']);
            $table->dropForeign(['city_code']);
            $table->dropForeign(['district_code']);
            $table->dropForeign(['village_code']);

            $table->dropColumn([
                'phone',
                'gender',
                'address',
                'province_code',
                'city_code',
                'district_code',
                'village_code',
                'ref_country_id'
            ]);
        });
    }
}
