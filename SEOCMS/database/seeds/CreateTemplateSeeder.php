<?php

use Illuminate\Database\Seeder;
use App\Models\Template;

class CreateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Template::class,  2000)->create();
    }
}
