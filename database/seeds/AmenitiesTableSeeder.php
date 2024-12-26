<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('amenities')->delete();

        DB::table('amenities')->insert(array (
            0 =>
            array (
                'id' => 5,
                'name' => 'Private Pool',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            1 =>
            array (
                'id' => 6,
                'name' => 'Area Pool',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            2 =>
            array (
                'id' => 7,
                'name' => 'Area Tennis',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            3 =>
            array (
                'id' => 8,
                'name' => 'Elevator',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            4 =>
            array (
                'id' => 9,
                'name' => 'Controlled Access',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            5 =>
            array (
                'id' => 10,
                'name' => 'Spa',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            6 =>
            array (
                'id' => 11,
                'name' => 'Patio',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            7 =>
            array (
                'id' => 12,
                'name' => 'Sprinkler',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            8 =>
            array (
                'id' => 13,
                'name' => 'Green Certified',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            9 =>
            array (
                'id' => 14,
                'name' => 'Handicap Access',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            10 =>
            array (
                'id' => 15,
                'name' => 'Waterfront',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            11 =>
            array (
                'id' => 16,
                'name' => 'Waterview',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            12 =>
            array (
                'id' => 17,
                'name' => 'Wooded',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            13 =>
            array (
                'id' => 18,
                'name' => 'Cul de Sac',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            14 =>
            array (
                'id' => 19,
                'name' => 'On Golf Course',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
            15 =>
            array(
                'id' => 20,
                'name' => 'Elevator Shaft',
                'description' => NULL,
                'created_at' => '2020-03-12 07:45:58',
                'updated_at' => '2020-03-12 07:45:58',
            ),
        ));
    }
}
