<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\BankAccount;
use October\Rain\Database\Updates\Seeder;

class SeedRefBankAccountsTable extends Seeder
{
    public function run()
    {
        BankAccount::create([
            'id'          => 1,
            'ref_bank_id' => 1,
            'acc_name'    => 'Agung TSP',
            'acc_number'  => '1001000',
        ]);

        BankAccount::create([
            'id'          => 2,
            'ref_bank_id' => 2,
            'acc_name'    => 'Agung TSP',
            'acc_number'  => '2001000',
        ]);
    }
}
