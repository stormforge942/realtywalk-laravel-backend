<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('zones')->delete();

        DB::table('zones')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'United States',
                'code' => 'us',
                'lat' => NULL,
                'lng' => NULL,
                '_lft' => 1,
                '_rgt' => 12,
                'parent_id' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Texas',
                'code' => 'tx',
                'lat' => NULL,
                'lng' => NULL,
                '_lft' => 2,
                '_rgt' => 11,
                'parent_id' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Austin',
                'code' => 'aus',
                'lat' => '30.266666',
                'lng' => '-97.733330',
                '_lft' => 3,
                '_rgt' => 4,
                'parent_id' => 2,
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Dallas',
                'code' => 'dal',
                'lat' => '32.7815020',
                'lng' => '-96.7998500',
                '_lft' => 5,
                '_rgt' => 6,
                'parent_id' => 2,
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Houston',
                'code' => 'hou',
                'lat' => '29.7572240',
                'lng' => '-95.3606410',
                '_lft' => 7,
                '_rgt' => 8,
                'parent_id' => 2,
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'San Antonio',
                'code' => 'san',
                'lat' => '32.7815020',
                'lng' => '-96.7998500',
                '_lft' => 9,
                '_rgt' => 10,
                'parent_id' => 2,
            ),
        ));


    }
}
