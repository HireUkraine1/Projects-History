<?php

use Illuminate\Database\Seeder;
use App\Models\Route;

class CreateRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Route::class, 50)->create();
    }
}
