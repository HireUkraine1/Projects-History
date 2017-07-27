<?php

use Illuminate\Database\Seeder;
use Illuminate\sportport\Facades\DB;

class AdditionSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'main_cattegory_price',
            'name' => 'Main Category Price',
            'description' => 'First Category Price',
            'value' => 100,
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'addition_cattegory_price',
            'name' => 'Addition Category Price',
            'description' => 'Addition Category Price',
            'value' => 50,
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);
    }
}



