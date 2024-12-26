<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();

        DB::table('categories')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Townhouse/Condo',
                'created_at' => '2020-03-10 13:46:29',
                'updated_at' => '2020-03-10 13:46:29',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Single-Family',
                'created_at' => '2020-03-10 13:46:29',
                'updated_at' => '2020-03-10 13:46:29',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Mid/Hi-Rise Condo',
                'created_at' => '2020-03-10 13:46:29',
                'updated_at' => '2020-03-10 13:46:29',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Country Homes/Acreage',
                'created_at' => '2020-03-10 13:46:29',
                'updated_at' => '2020-03-10 13:46:29',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Multi-Family',
                'created_at' => '2020-03-10 13:46:29',
                'updated_at' => '2020-03-10 13:46:29',
            ),
        ));
    }
}
