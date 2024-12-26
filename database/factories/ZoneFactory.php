<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Zone;
use Faker\Generator as Faker;

$factory->define(Zone::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'lat' => $faker->word,
        'lng' => $faker->word,
        'type' => $faker->randomElement(),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
