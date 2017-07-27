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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Recipes::class, function (Faker\Generator $faker) {
    $name = $faker->name;
    return [
        'name' => $name,
        'name_seo' => str_slug($name),
        'vimeo_url' => 'https://vimeo.com/' . $faker->uuid,
        'description' => $faker->paragraph(3),
        'locale' => $faker->locale,
    ];
});
