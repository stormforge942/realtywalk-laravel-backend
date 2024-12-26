<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Polygon;
use Faker\Generator as Faker;

$factory->define(Polygon::class, function (Faker $faker) {

    return [
        'color' => $faker->word,
        'title' => $faker->word,
        'lat' => $faker->word,
        'lng' => $faker->word,
        'zoom' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
