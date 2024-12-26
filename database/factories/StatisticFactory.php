<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Statistic;
use Faker\Generator as Faker;

$factory->define(Statistic::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'descr' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
