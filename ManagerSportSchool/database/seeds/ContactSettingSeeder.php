<?php

use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'contact_sport_email',
            'name' => 'Contact sport Email',
            'description' => 'Contact sport Email',
            'value' => 'info@sport.com',
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'contact_sport_email',
            'name' => 'Contact sport Email',
            'description' => 'Contact sport Email',
            'value' => 'info@sport.com',
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'contact_sport_email',
            'name' => 'Contact sport Email',
            'description' => 'Contact sport Email',
            'value' => 'info@sport.com',
            'field' => '{"name":"value","label":"Value","type":"text"}',
            'active' => 1,
        ]);
    }
}
