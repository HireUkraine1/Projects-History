<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Admin::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Models\Route::class, function (Faker\Generator $faker) {

    return [
        'slug' => $faker->slug,
        'alias' => rand(0,1) ? $faker->slug : "",
    ];
});



$factory->define(App\Models\DomainAlias::class, function (Faker\Generator $faker) {

    return [
        'domain_url' => $faker->url,
        'robotstxt' => "",
        'master' => false,
    ];
});

$factory->define(App\Models\Template::class, function (Faker\Generator $faker) {

    return [
        'virtualroot' => $faker->slug,
        'name' => $faker->slug,
        'body' => $faker->randomHtml(4 ,4),
    ];
});