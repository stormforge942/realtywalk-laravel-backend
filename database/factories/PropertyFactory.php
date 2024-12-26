<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Property;
use Faker\Generator as Faker;

$factory->define(Property::class, function (Faker $faker) {

    return [
        'builder_id' => $faker->randomDigitNotNull,
        'mls_number' => $faker->randomDigitNotNull,
        'polygon_id' => $faker->word,
        'property_type' => $faker->word,
        'lat' => $faker->word,
        'lng' => $faker->word,
        'address_number' => $faker->word,
        'address_street' => $faker->word,
        'zipcode' => $faker->word,
        'title' => $faker->word,
        'descr' => $faker->text,
        'style' => $faker->word,
        'year_built' => $faker->word,
        'sqft' => $faker->randomDigitNotNull,
        'lot_size' => $faker->word,
        'bedrooms' => $faker->word,
        'bathrooms_full' => $faker->word,
        'bathrooms_half' => $faker->word,
        'status' => $faker->word,
        'levels' => $faker->randomDigitNotNull,
        'price_from' => $faker->word,
        'garage_capacity' => $faker->word,
        'private_pool' => $faker->word,
        'area_pool' => $faker->word,
        'area_tennis' => $faker->word,
        'elevator' => $faker->word,
        'controlled_access' => $faker->word,
        'spa' => $faker->word,
        'patio' => $faker->word,
        'sprinkler' => $faker->word,
        'green_certified' => $faker->word,
        'handicap_access' => $faker->word,
        'waterfront' => $faker->word,
        'waterview' => $faker->word,
        'wooded' => $faker->word,
        'cul_de_sac' => $faker->word,
        'on_golf_course' => $faker->word,
        'acres' => $faker->word,
        'youtube_embed' => $faker->word,
        'finance_type' => $faker->word,
        'hoa_annual_fee' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
