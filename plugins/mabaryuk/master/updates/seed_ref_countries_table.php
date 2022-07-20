<?php namespace MabarYuk\Master\Updates;

Use Schema;
Use October\Rain\Database\Updates\Seeder;
Use MabarYuk\Master\Models\Country;
use League\Csv\Reader;

Class SeedCountryTable extends Seeder
{
    public function run()
    {
        $reader = Reader::createFromPath('files/countries.csv', 'r');
        $counter = 0;

        foreach ($reader as $row) {
            if ($counter++ > 0) {
                Country::create([
                    'id'         => $row[0],
                    'nationality'=> $row[1],
                    'alias'      => $row[2],
                    'name'       => $row[3],
                    'code'       => $row[4] ?? NULL,
                    'iso'        => $row[5],
                    'created_by' => 1
                ]);
            }
        }
    }
}