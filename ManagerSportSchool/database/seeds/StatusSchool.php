<?php

use App\Models;
use Illuminate\Database\Seeder;

class StatusSchool extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Models\SchoolStatus::create([
            'id' => 1,
            'name' => 'Application treating',
        ]);

        Models\SchoolStatus::create([
            'id' => 2,
            'name' => 'Documents treating',
        ]);

        Models\SchoolStatus::create([
            'id' => 3,
            'name' => 'Approved',
        ]);
    }
}
