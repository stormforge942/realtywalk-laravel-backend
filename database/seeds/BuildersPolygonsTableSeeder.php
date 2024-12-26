<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildersPolygonsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('builder_polygon')->delete();

        DB::table('builder_polygon')->insert(array (
            0 =>
            array (
                'builder_id' => 5,
                'polygon_id' => 10,
            ),
            1 =>
            array (
                'builder_id' => 237,
                'polygon_id' => 25,
            ),
            2 =>
            array (
                'builder_id' => 308,
                'polygon_id' => 9,
            ),
            3 =>
            array (
                'builder_id' => 103,
                'polygon_id' => 9,
            ),
            4 =>
            array (
                'builder_id' => 3,
                'polygon_id' => 77,
            ),
            5 =>
            array(
                'builder_id' => 1713,
                'polygon_id' => 2025,
            ),
            6 =>
            array(
                'builder_id' => 15,
                'polygon_id' => 2025,
            ),
            7 =>
            array (
                'builder_id' => 317,
                'polygon_id' => 13,
            ),
            8 =>
            array (
                'builder_id' => 1,
                'polygon_id' => 55,
            ),
            9 =>
            array (
                'builder_id' => 4,
                'polygon_id' => 663,
            ),
            10 =>
            array (
                'builder_id' => 2,
                'polygon_id' => 444,
            ),
            11 =>
            array (
                'builder_id' => 301,
                'polygon_id' => 27,
            ),
        ));


    }
}
