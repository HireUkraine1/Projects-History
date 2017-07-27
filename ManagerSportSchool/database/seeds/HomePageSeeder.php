<?php

use App\Models;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Models\Page::create([
            'name' => 'Home',
            'slug' => 'home',
            'meta_title' => 'Home',
            'meta_description' => 'Home',
            'meta_keywords' => 'Home',
            'content' => '<p>sport — Setting the World Standard in sporting and sport Education. sport is the world’s leading education and membership organisation. sport Instructors are trained to the highest level in outdoor safety and education. sport activity schools and centres are audited each year to ensure high standards and your safety. sport Training programs are designed to make it easy to learn and to have safe experiences.</p>',
            'thumbnail' => '/img/empty.jpeg',
            'status' => 'PUBLISHED',
            'search_form' => 'SEARCH SCHOOL',
            'slogan' => 'Enable',
            'baner_image' => '/img/home-banner.jpg',
            'baner_text' => "Connecting You With Quality and \n Safe Activity Centres and Training Programs",
            'is_homepage' => 'True'
        ]);
    }
}

