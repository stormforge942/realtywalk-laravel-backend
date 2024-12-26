<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Amenity;
use Faker\Generator as Faker;

$factory->define(Amenity::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'descr' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
