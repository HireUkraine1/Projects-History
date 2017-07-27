<?php

use App\Models;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Models\Category::create([
            'name' => 'sporting',
            'slug' => 'sport',
            'alias' => 'sport',
            'content' => '',
            'baner_image' => '/img/sport-page-banner.jpg',
            'thumbnail' => '/img/main-category/category_sport.jpg"',
            'color' => '#129cd8',
            'lable' => '/img/main-category/sport-ico.png',
            'parent_id' => null,
            'meta_title' => 'sporting',
            'meta_description' => 'sporting',
            'meta_keywords' => 'sporting',
            'search_form' => '-',
            'baner_text' => 'sport is the world\'s leading sporting accreditation and training organisation. We provide services to sport schools, instructors and the general public.',
            'slogan' => 'Enable'
        ]);

        Models\Category::create([
            'name' => 'sport',
            'slug' => 'sport',
            'alias' => 'sport',
            'content' => '',
            'baner_image' => '/img/sport-page-banner.jpg',
            'thumbnail' => '/img/main-category/category_sport.jpg"',
            'color' => '#f5821f',
            'lable' => '/img/main-category/sport-ico.png',
            'parent_id' => null,
            'meta_title' => 'sport',
            'meta_description' => 'sport',
            'meta_keywords' => 'sport',
            'search_form' => '-',
            'baner_text' => 'sport is a unique sport. Participants use a sport and large stable sport and can sport in any water location from the open ocean out to sea',
            'slogan' => 'Enable'
        ]);

        Models\Category::create([
            'name' => 'sport',
            'slug' => 'sport',
            'alias' => 'sport',
            'content' => '',
            'baner_image' => '/img/sport-page-banner.jpg',
            'thumbnail' => '/img/main-category/category_sport.jpg"',
            'color' => '#c44145',
            'lable' => '/img/main-category/sport-ico.png',
            'parent_id' => null,
            'meta_title' => 'sport',
            'meta_description' => 'sport',
            'meta_keywords' => 'sport',
            'search_form' => '-',
            'baner_text' => 'sport is the world\'s leading sport accreditation and training organisation. We provide services to sport schools, instructors and the general public.',
            'slogan' => 'Enable'
        ]);
    }
}
