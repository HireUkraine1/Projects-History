<?php

use Illuminate\Database\Seeder;
use Illuminate\sportport\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'header_logo',
            'name' => 'Header Logo',
            'description' => 'Header Logo',
            'value' => '/img/logo.png',
            'field' => '{"name":"value","label":"Value","type":"browse_image"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'header_menu',
            'name' => 'Header Menu',
            'description' => 'Header Menu',
            'value' => '[{"name":"Find School","link":"school"},{"name":"For Instructors","link":"#"},{"name":"Membership","link":"#"},{"name":"Contacts","link":"#"}]',
            'field' => '{"name":"value","label":"Options","type":"table","entity_singular":"option","columns":{"name":"Name","link":"Link"},"max":4,"min":0}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_logo',
            'name' => 'Footer Logo',
            'description' => 'Footer Logo',
            'value' => '/img/footer-logo.png',
            'field' => '{"name":"value","label":"Value","type":"browse_image"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_menu_column_1',
            'name' => 'Footer Menu Column #1',
            'description' => 'Footer Menu Column #1',
            'value' => '[{"name":"sport","link":"sport"},{"name":"sport","link":"sport"},{"name":"sport","link":"sport"}]',
            'field' => '{"name":"value","label":"Options","type":"table","entity_singular":"option","columns":{"name":"Name","link":"Link"},"max":3,"min":0}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_menu_column_2',
            'name' => 'Footer Menu Column #2',
            'description' => 'Footer Menu Column #2',
            'value' => '[{"name":"Complaints","link":"#"},{"name":"Privacy","link":"#"},{"name":"Refund","link":"#"}]',
            'field' => '{"name":"value","label":"Options","type":"table","entity_singular":"option","columns":{"name":"Name","link":"Link"},"max":3,"min":0}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_menu_column_3',
            'name' => 'Footer Menu Column #3',
            'description' => 'Footer Menu Column #3',
            'value' => '[{"name":"About","link":"#"},{"name":"Feedback","link":"#"},{"name":"Copyright","link":"#"}]',
            'field' => '{"name":"value","label":"Options","type":"table","entity_singular":"option","columns":{"name":"Name","link":"Link"},"max":3,"min":0}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_menu_column_4',
            'name' => 'Footer Menu Column #4',
            'description' => 'Footer Menu Column #4',
            'value' => '[{"name":"Membership","link":"#"},{"name":"Contacts","link":"#"}]',
            'field' => '{"name":"value","label":"Options","type":"table","entity_singular":"option","columns":{"name":"Name","link":"Link"},"max":3,"min":0}',
            'active' => 1,
        ]);


        DB::table('settings')->insert([
            'key' => 'footer_email',
            'name' => 'Footer Email',
            'description' => 'Footer Email',
            'value' => 'info@sport.com',
            'field' => '{"name":"value","label":"Value","type":"email"}',
            'active' => 1,
        ]);

        DB::table('settings')->insert([
            'key' => 'footer_fb_link',
            'name' => 'Footer Facebook Link',
            'description' => 'Footer Facebook Link',
            'value' => 'http://facebook.com/sport.Intl',
            'field' => '{"name":"value","label":"Value","type":"url"}',
            'active' => 1,
        ]);

    }
}



