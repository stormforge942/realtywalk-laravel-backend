<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Builder;
use Faker\Generator as Faker;

$factory->define(Builder::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'slug' => $faker->word,
        'descr' => $faker->text,
        'email' => $faker->text,
        'address1' => $faker->word,
        'address2' => $faker->word,
        'address3' => $faker->word,
        'city' => $faker->word,
        'phone' => $faker->word,
        'website' => $faker->word,
        'hidden' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
